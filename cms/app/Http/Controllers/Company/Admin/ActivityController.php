<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Employee;
use App\Activity;
use App\ActivityPriority;
use App\ActivityType;
use App\Http\Requests\ActivityRequest;
use DB;
use Session;
use Auth;
use View;
use Carbon\Carbon;
use Log;
use Validator;
use Barryvdh\DomPDF\Facade as PDF;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:activity-create', ['only' => ['create','store']]);
        $this->middleware('permission:activity-view');
        $this->middleware('permission:activity-update', ['only' => ['edit','update']]);
        $this->middleware('permission:activity-delete', ['only' => ['destroy']]);
        $this->middleware('permission:activity-status', ['only' => ['updateMark']]);
    }

    public function index(){
      $company_id = config('settings.company_id');
        $empId = Auth::user()->EmployeeId();
        if(Auth::user()->isCompanyManager()){
          $allSup = [];
        }else{
          $allSup = [];
          $allSup = Auth::user()->getUpChainUsers($empId,$allSup);
          array_pop($allSup);
          $empAdmins = Employee::where('company_id',$company_id)->select('id')->where('is_admin',1)->get();
          foreach($empAdmins as $emp){
            array_push($allSup,$emp->id);
          }          
        }
        $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(),array());
        if(Auth::user()->isCompanyManager())
          $activities = Activity::where('company_id',$company_id);
        else
        $activities = Activity::where('company_id',$company_id)->where(function($q)use($juniors,$empId){
          $q = $q->whereIn('activities.created_by',$juniors)->whereIn('activities.assigned_to',$juniors,'or')->orWhere('activities.created_by',$empId)->orWhere('activities.assigned_to',$empId);
        });

        $activityType = $activities->groupBy('type')->select('type')->get()->toArray();
        $activityType = ActivityType::whereIn('id',$activityType)->select('id','name')->get();
        $createdBy =  $activities->groupBy('created_by')->select('created_by')->get()->toArray();
        $createdBy = Employee::whereIn('id',$createdBy)->select('id','name')->get();
        $assignedTo = $activities->groupBy('assigned_to')->select('assigned_to')->get()->toArray();
        $assignedTo = Employee::whereIn('id',$assignedTo)->select('id','name')->get();
        $party = $activities->groupBy('client_id')->select('client_id')->get()->toArray();
        $parties = Client::whereIn('id',$party)->select('id','company_name')->get();
        $activitiesCount = $activities->get()->count();
        return view('company.activities.index', compact('activities','parties','activitiesCount','activityType','createdBy','assignedTo'));
    }

    public function ajaxTable(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'completion_datetime',
            2 =>'AssignedByName',
            3 => 'AssignedToName',
            4 => 'title',
            5 => 'PartyName',
            6 => 'TypeName',
            7 => 'PriorityName',
            8 => 'start_datetime',
            9 => 'action',
        );
        $empId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        if($request->assignedBy){
          $assignedBy = $request->assignedBy;
        }
        if($request->assignedTo){
          $assignedTo = $request->assignedTo;
        }
        if($request->client_id){
          $client_id = $request->client_id;
        }
        if($request->type){
          $type = $request->type;
        }
        if($request->startDate){
          $startDateFilter = $request->input('startDate');
        } 
        if($request->endDate){
          $endDateFilter = $request->input('endDate');
        }
        
        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        if(Auth::user()->isCompanyManager()){
          $allSup = [];
          $totalData = Activity::where('company_id',$company_id)->count();
        }else{
          $allSup = [];
          $allSup = Auth::user()->getUpChainUsers($empId,$allSup);
          array_pop($allSup);
          $empAdmins = Employee::where('company_id',$company_id)->select('id')->where('is_admin',1)->get();
          foreach($empAdmins as $emp){
            array_push($allSup,$emp->id);
          }
          $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(),array());
          $totalData = Activity::where('company_id',$company_id)->where(function($q)use($juniors,$empId){
            $q = $q->whereIn('activities.created_by',$juniors)->whereIn('activities.assigned_to',$juniors,'or')->orWhere('activities.created_by',$empId)->orWhere('activities.assigned_to',$empId);
          })->count();
        }
        $totalFiltered = $totalData; 
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $todayDate = Carbon::now()->format('Y-m-d');
        $activities =  Activity::select('activities.*','addedBy_tbl.id as AddedById','addedBy_tbl.name as AssignedByName','approvedBy_tbl.id as ApprovedById','approvedBy_tbl.name as AssignedToName','clients.id as ClientId','clients.company_name as PartyName','activity_types.id as type','activity_types.name as TypeName','activity_priorities.id as PriorityId','activity_priorities.name as PriorityName')
        ->leftJoin('employees as approvedBy_tbl','activities.assigned_to','approvedBy_tbl.id')
        ->leftJoin('employees as addedBy_tbl','activities.created_by','addedBy_tbl.id')
                  ->leftJoin('clients','activities.client_id','clients.id')
                  ->leftJoin('activity_types','activities.type','activity_types.id')
                  ->leftJoin('activity_priorities','activities.priority','activity_priorities.id')
                  ->where('activities.company_id',$company_id);
                  if(!(Auth::user()->isCompanyManager())){
                      $activities = $activities->where(function($q)use($juniors,$empId){
                        $q = $q->whereIn('activities.created_by',$juniors)->whereIn('activities.assigned_to',$juniors,'or')->orWhere('activities.created_by',$empId)->orWhere('activities.assigned_to',$empId);
                      });
                  }
                  $activities = $activities->where(function($q1)use($request,$today,$todayDate){
                    if(isset($request->ftype[0]) && $request->ftype[0]==1){
                      $q1 = $q1->where('activities.start_datetime','>=',$todayDate)->whereNull('activities.completion_datetime');
                    }
                    if(isset($request->ftype[1]) && $request->ftype[1]==1){
                        $q1 = $q1->where('activities.start_datetime','<',$todayDate)->where('activities.completion_datetime','=',NULL);
                    }
                    if(isset($request->ftype[2]) && $request->ftype[2]==1){
                      if($request->ftype[0]==1 || $request->ftype[1]==1 ){
                        $q1 = $q1->orWhere('activities.completion_datetime','!=',Null);
                      }else{
                        $q1 = $q1->where('activities.completion_datetime','!=',Null);
                      }
                    }
                    if(isset($request->ftype[3]) && $request->ftype[3]==1){
                      if($request->ftype[0]==1 || $request->ftype[1]==1 || $request->ftype[2]==1 ){
                        $q1 = $q1->orWhere('activities.start_datetime','=',$todayDate);
                      }else{
                        $q1 = $q1->where('activities.start_datetime','>=',$todayDate)
                              ->where('activities.start_datetime','<=',$todayDate.' 23:59:59');
                      }
                    }  
                  });

                  if($request->assignedBy){
                    $activities = $activities->where('activities.created_by',$assignedBy);
                  }
                  if($request->assignedTo){
                    $activities = $activities->where('activities.assigned_to',$assignedTo);
                  }
                  if($request->client_id){
                    $activities = $activities->where('activities.client_id',$client_id);
                  }
                  if($request->type){
                    $activities = $activities->where('activities.type',$type);
                  }
                  if($request->startDate){
                    $activities = $activities->where('activities.start_datetime','>=',$startDateFilter);
                  }
                  if($request->endDate){
                    $activities = $activities->where('activities.start_datetime','<=',$endDateFilter.' 23:59:59');
                  }
                  if($request->input('search.value')){
                    $activities = $activities->where(function($q3)use($search){
                      $q3 = $q3->where('activities.id','LIKE',"%{$search}%")
                              ->orWhere('activities.title', 'LIKE',"%{$search}%")
                              ->orWhere('clients.company_name', 'LIKE',"%{$search}%")
                              ->orWhere('activity_types.name', 'LIKE',"%{$search}%")
                              ->orWhere('activity_priorities.name', 'LIKE',"%{$search}%")
                              ->orWhere('addedBy_tbl.name', 'LIKE',"%{$search}%")
                              ->orWhere('approvedBy_tbl.name', 'LIKE',"%{$search}%")
                              ->orWhere('activities.start_datetime', 'LIKE',"%{$search}%");
                    });
                  }
                  
                  $totalFiltered = $activities->get()->count();
                  $activities = $activities->orderBy($order,$dir)->offset($start)
                              ->limit($limit)
                              ->get();
        $data = array();
        if(!empty($activities))
        {   
            $i = $start;
            foreach ($activities as $activity)
            {
                $show =  domain_route('company.admin.activities.show',[$activity->id]);
                $edit =  domain_route('company.admin.activities.edit',[$activity->id]);

                $nestedData['id'] = ++$i;
                if($activity->completion_datetime!=NULL){
                  $checked = 'checked="checked"';
                  $checkedStatus = "no_uncheck";
                }else{
                  $checked = '';
                  $checkedStatus = "no_check";
                }
                if(Auth::user()->can('activity-status') && (Auth::user()->isCompanyManager() || Auth::user()->EmployeeId()==$activity->created_by || Auth::user()->EmployeeId()==$activity->assigned_to))
                $nestedData['completion_datetime'] = '<div class="round"><input type="checkbox" id="act'.$activity->id.'" class="check check_'.$activity->id.'" name="status" value="'.$activity->id.'" '.$checked.'><label for="act'.$activity->id.'"></label></div>';
                else
                  $nestedData['completion_datetime'] = '<div class="round"><input type="checkbox" id="act'.$activity->id.'" readonly class="'.$checkedStatus.'" name="status" value="'.$activity->id.'" '.$checked.'><label for="act'.$activity->id.'"></label></div>';

                $nestedData['title'] = $activity->title;

                if(isset($activity->client_id)){
                  $access = DB::table('handles')->where('company_id',$company_id)->where('employee_id',Auth::user()->EmployeeId())->where('client_id',$activity->client_id)->first();
                  if($access){
                      $nestedData['PartyName'] = '<a href="'.domain_route('company.admin.client.show',[$activity->client_id]).'">'.$activity->PartyName.'</a>';
                  }else{
                      $nestedData['PartyName'] = '<a class="alert_party_model" href="#">'.$activity->PartyName.'</a>';
                  }
                }else{
                  $nestedData['PartyName'] = '';
                }

                $nestedData['TypeName'] = $activity->TypeName;
                $nestedData['PriorityName'] = $activity->PriorityName;
                $nestedData['start_datetime'] = getDeltaDate(Carbon::parse($activity->start_datetime)->format('Y-m-d'));

                if(isset($activity->created_by)){
                    if(Auth::user()->isCompanyManager() || in_array($activity->created_by,$juniors)){
                      $nestedData['AssignedByName'] = '<a href="'.domain_route('company.admin.employee.show',[$activity->created_by]).'" datasalesman="'. $activity->AssignedByName .'">'. $activity->AssignedByName.'</a>';
                    }else{
                      $nestedData['AssignedByName'] = '<a href="#" class="alert-modal" datasalesman="'. $activity->AssignedByName .'">'. $activity->AssignedByName.'</a>';
                    }
                }else{
                    $nestedData['AssignedByName'] = '';
                }

                if(isset($activity->assigned_to)){
                    if(Auth::user()->isCompanyManager() || in_array($activity->assigned_to,$juniors)){
                        $nestedData['AssignedToName'] = '<a href="'.domain_route('company.admin.employee.show',[$activity->assigned_to]).'" datasalesman="'. $activity->AssignedToName .'">'. $activity->AssignedToName.'</a>';
                    }else{
                        $nestedData['AssignedToName'] = '<a href="#" class="alert-modal" datasalesman="'. $activity->AssignedToName .'">'. $activity->AssignedToName.'</a>';
                    }
                }else{
                    $nestedData['AssignedToName'] = '';
                }

                $nestedData['action']='<a style="color:green;font-size: 15px;margin-left:5px;" href="'.$show.'" class="" style=""><i class="fa fa-eye"></i></a>';
                if(Auth::user()->can('activity-update')){
                  if(Auth::user()->isCompanyManager() || $empId== $activity->created_by || $empId == $activity->assigned_to)
                    $nestedData['action']=$nestedData['action'].'<a style="color:#f0ad4e!important;font-size: 15px;margin-left:5px;" href="'.$edit.'"><i class="fa fa-edit"></i></a>';
                }
                if((Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) || ($activity->created_by==Auth::user()->EmployeeId() && (Auth::user()->can('activity-delete'))))
                  $nestedData['action']=$nestedData['action'].'<a style="color:red;font-size: 15px;margin-left:5px;  " data-mid="{{ $activity->id }}" data-url="'.domain_route('company.admin.activities.destroy', [$activity->id]) .'" data-toggle="modal" data-target="#delete" style=""><i class="fa fa-trash-o"></i></a>';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,
                    );
            
        echo json_encode($json_data); 
    }

    public function custompdfdexport(Request $request)
    {
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      set_time_limit(300);
      ini_set("memory_limit", "256M");
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.activities.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function updateMark(Request $request)
    {
        $company_id = config('settings.company_id');
        $empId = Auth::user()->EmployeeId();
        if(Auth::user()->isCompanyManager()){
          $activity = Activity::where('company_id',$company_id)->where('id',$request->id)->first();
        }else{
          $activity = Activity::where('company_id',$company_id)->where(function($q)use($empId){
            $q = $q->where('created_by',$empId)->orWhere('assigned_to',$empId);
          })->where('id',$request->id)->first();
        }
        if ($activity) {
            if ($request->checked=="true") {
                $activity->completion_datetime = date('Y-m-d H:i:s');
                $activity->completed_by = Auth::user()->EmployeeId();
                $data['ticked'] = true;
            } else {
                $activity->completion_datetime = NULL;
                $activity->completed_by = Null;
                $data['ticked'] = false;
            }
            $saved = $activity->save();
            if($saved){
                // if($activity->created_by==$activity->assigned_to){
                //   $fbID = getFBIDs($company_id, null, $activity->assigned_to);
                // }else{
                //   $emps = [$activity->assigned_to,$activity->created_by];
                //   $fbID = Employee::whereIn('id',$emps)->pluck('firebase_token')->toArray();
                // }

                $notificationData = array(
                    "company_id" => $company_id,
                    "employee_id" => $activity->assigned_to,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => "Activity Updated",
                    "description" => $activity->title,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );
                
                $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");


                $activity->company_name = getObjectValue($activity->client,"company_name","N/A");
                $activity->priority_name = getObjectValue($activity->activityPriority,"name","");
                $activity->type_name = getObjectValue($activity->activityType,"name","");
                $activity->created_by_name = Employee::where('id',$activity->created_by)->first()->name;
                $activity->assigned_to_name = Employee::where('id',$activity->assigned_to)->first()->name;

                $assigned_to_superiors = Employee::employeeParents((int)$activity->assigned_to, array());
                $created_by_superiors = $activity->assigned_to==$activity->created_by?array():Employee::employeeParents((int)$activity->created_by, array());

                $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
                $mergedSuperiorsTokens = Employee::where('company_id', $company_id)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();
                
                $created_by_token = $mergedSuperiorsTokens[$activity->created_by];
                $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                unset($mergedSuperiorsTokens[$activity->assigned_to]);   
                if($created_by_token != $assigned_to_token) unset($mergedSuperiorsTokens[$activity->created_by]);   
                
                if(!empty(array_values($mergedSuperiorsTokens))) $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, $notificationData, $dataPayload);

                if($activity->assigned_to == Auth::user()->EmployeeId()) {
                  sendPushNotification_(array($assigned_to_token), 17, null, $dataPayload);
                }else{
                  sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                }

                if($activity->created_by!=$activity->assigned_to){
                  if($activity->created_by== Auth::user()->EmployeeId()) sendPushNotification_(array($created_by_token), 17, null, $dataPayload);
                  else sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
                }
                
                // if($activity->created_by!=$activity->assigned_to){
                //   $created_by_token = $mergedSuperiorsTokens[$activity->created_by];
                //   $notificationDataSend = $notificationData;
                //   if($activity->created_by== Auth::user()->EmployeeId()) $notificationDataSend = null;
                //   unset($mergedSuperiorsTokens[$activity->created_by]);
                //   sendPushNotification_(array($created_by_token), 17, $notificationDataSend, $dataPayload);
                // }
                // if(!empty(array_values($mergedSuperiorsTokens))) $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);

                // $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);

                // $sent = sendPushNotification_($fbID, 17, $notificationData, $dataPayload);
                // $sent = sendPushNotification_(getFBIDs($company_id), 17, $notificationData, $dataPayload);
            }
            $data['result'] = true;
        }else{
            $data['result'] = false;
        }
        return $data;
    }

    public function create()
    {
        $data = [];
        $company_id = config('settings.company_id');
        $data['users'] = Employee::where('company_id',$company_id)->orderBy('name', 'ASC')->select("name", "id")->where('status','Active')->get();
        $data['activityPriorities'] = ActivityPriority::where('company_id', $company_id)->withTrashed()->pluck("name", "id");
        $data['activityType'] = ActivityType::where('company_id', $company_id)->select("name", "id")->withTrashed()->get();
        if(Auth::user()->isCompanyManager()){
          $data['clients'] = Client::where('company_id',$company_id)->where('status','Active')->get();
        }else{
          $tempReq = new Request;
          $tempReq->created_by = Auth::user()->EmployeeId();
          $clientIds = $this->getCommonParties(null,$tempReq,true);
          $data['clients'] = Client::where('company_id',$company_id)->whereIn('id',$clientIds)->where('status','Active')->get();
        }
        return view('company.activities.create', $data);
    }//end create

    public function store(ActivityRequest $request)
    {
        $company_id = config('settings.company_id');
        $validator = Validator::make($request->all(), [
          'start_date_ad'=>'date_format:Y-m-d',
          'start_date'=>'date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
        }
        $input = $request->all();
        $input['company_id'] = $company_id;
        $input['created_by'] = Auth::user()->EmployeeId();
        $input['start_datetime'] = setDateTime($request->start_date_ad . ' ' . date("H:i:s", strtotime($request->start_time)), true);
        if ($request->linkedTo == 'none') {
            $input['client_id'] = NULL;
        }
        if ($request->status) {
            $input['completion_datetime'] = date('Y-m-d H:i:s');
            $input['completed_by'] = Auth::user()->EmployeeId();
        }

        $saved = Activity::create($input);
        if ($saved) {
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $saved->assigned_to,
                "data_type" => "activity",
                "data" => "",
                "action_id" => $saved->id,
                "title" => "A new activity has been assigned to you",
                "description" => $saved->title,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $saved->company_name = getObjectValue($saved->client,"company_name","N/A");
            $saved->priority_name = getObjectValue($saved->activityPriority,"name","");
            $saved->type_name = getObjectValue($saved->activityType,"name","");
            $saved->created_by_name = Employee::where('id',$saved->created_by)->first()->name;
            $saved->assigned_to_name = Employee::where('id',$saved->assigned_to)->first()->name;
            if ($request->status) {
                $saved->completion_datetime = date('Y-m-d H:i:s');
                $saved->completed_by = Auth::user()->EmployeeId();
            }else{
                $saved->completion_datetime = null;
                $saved->completed_by = null;
            }

            $assigned_to_superiors = Employee::employeeParents((int)$saved->assigned_to, array());
            $created_by_superiors = $saved->assigned_to==$saved->created_by?array():Employee::employeeParents((int)$saved->created_by, array());
            $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
            $mergedSuperiorsTokens = Employee::where('company_id', $company_id)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();
            
            $dataPayload = array("data_type" => "activity", "activity" => $saved, "action" => "add");
            
            $created_by_token = $mergedSuperiorsTokens[$saved->created_by];
            $assigned_to_token = $mergedSuperiorsTokens[$saved->assigned_to];
            unset($mergedSuperiorsTokens[$saved->assigned_to]);   
            if($created_by_token != $assigned_to_token) unset($mergedSuperiorsTokens[$saved->created_by]);   
            
            if(!empty(array_values($mergedSuperiorsTokens))) $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);

            if($saved->assigned_to == Auth::user()->EmployeeId()) {
              sendPushNotification_(array($assigned_to_token), 17, null, $dataPayload);
            }else{
              sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
            }

            if($saved->created_by!=$saved->assigned_to){
              if($saved->created_by == Auth::user()->EmployeeId()) sendPushNotification_(array($created_by_token), 17, null, $dataPayload);
              else sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
            }


            // $assigned_to_token = $mergedSuperiorsTokens[$saved->assigned_to];
            // unset($mergedSuperiorsTokens[$saved->assigned_to]);

            // $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
            
            // $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
        }
        Session::flash('success', 'Activity created successfully.');
        Session::flash('alert-class', 'alert-success');
        return response()->json(['message' => 'Activity created successfully.', 'url' => domain_route('company.admin.activities.index')], 200);
    }//end store

    public function show($domain, $id)
    {
        $company_id = config('settings.company_id');
        $empId =  Auth::user()->employeeId();
        if(Auth::user()->isCompanyManager()){
            $row = Activity::where('company_id',$company_id)->where('id',$id)->first();
        }else{
            $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(),array());
            $handles = DB::table('handles')->where('employee_id',$empId)->where('company_id',$company_id)->pluck('client_id');
            $row = Activity::where('company_id',$company_id)->where(function($q)use($empId,$juniors,$handles){
              $q = $q->whereIn('activities.created_by',$juniors)->whereIn('activities.assigned_to',$juniors,'or')->whereIn('activities.client_id',$handles,'or')->orWhere('activities.created_by',$empId)->orWhere('activities.assigned_to',$empId);
            })->where('id',$id)->first();
        }
        if (!$row) {
            Session::flash('success', "Sorry! This activity doesn't exist or you don't have permission to view it.");
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }

        $action = null;

        if(Auth::user()->can('activity-update')){
          if(Auth::user()->isCompanyManager() || $empId== $row->created_by || $empId == $row->assigned_to) $action = '<a class="btn btn-warning btn-sm edit"  style="padding: 7px 6px;" href="'.domain_route('company.admin.activities.edit', [$id]).'"><i class="fa fa-edit"></i>Edit</a>';
        }

        if((Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) || ($row->created_by==Auth::user()->EmployeeId() && (Auth::user()->can('activity-delete'))))
          $action = $action.'<a class="btn btn-danger btn-sm delete" style="color:red;font-size: 15px;margin-left:5px;padding:7px 6px" data-mid="{{ $activity->id }}" data-url="'.domain_route('company.admin.activities.destroy', [$id]) .'" data-toggle="modal" data-target="#delete"><i class="fa fa-trash-o"></i>Delete</a>';


        return view('company.activities.show', compact('row', 'action'));
    }//end show

    public function edit($domain, $id)
    {
        $company_id = config('settings.company_id');
        $empId =  Auth::user()->employeeId();
        $data=[];
        if(Auth::user()->isCompanyManager()){
          $row = Activity::where('company_id',$company_id)->where('id',$id)->first();
        }else{
            $row = Activity::where('company_id',$company_id)->where(function($q)use($empId){
              $q = $q->where('created_by',$empId)->orWhere('assigned_to',$empId);
            })->where('id',$id)->first();
        }
        if (!$row && !(Auth::user()->isCompanyManager())) {
            Session::flash('success', "You are not authorized to edit this activity.");
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }
        $row->start_time = $row->start_datetime;
        $tempReq = new Request;
        $tempReq->created_by = $row->created_by;
        $tempReq->employee_id = $row->assigned_to;
        $editAccess = false;
        if(Auth::user()->isCompanyManager() || Auth::user()->EmployeeId()==$tempReq->created_by){
          $editAccess = true;
        }
        $data['editAccess'] = $editAccess;
        $data['row'] = $row;
        $data['users'] = Employee::where('company_id',$company_id)->where(function($q) use($row)  {
         $q->orWhere('id', $row->assigned_to);
         $q->orWhere('status','Active'); 
        })->get();
        $data['activityPriorities'] = ActivityPriority::where('company_id', $company_id)->withTrashed()->pluck("name", "id");
        $data['activityType'] = ActivityType::where('company_id', $company_id)->select("name", "id")->withTrashed()->get();
        $clientIds = $this->getCommonParties(null,$tempReq,true);
        if(isset($row->client_id))
          array_push($clientIds,$row->client_id);
        $data['clients'] = Client::where('company_id',$company_id)->whereIn('id',$clientIds)->get();
        return view('company.activities.edit', $data);
    }//end edit

    public function update($domain, $id, Request $request)
    {
        $company_id = config('settings.company_id');
        $empId =  Auth::user()->employeeId();
        if(Auth::user()->isCompanyManager()){
          $row = Activity::where('company_id',$company_id)->where('id',$id)->first();
        }else{
            $row = Activity::where('company_id',$company_id)->where(function($q)use($empId){
              $q = $q->where('created_by',$empId)->orWhere('assigned_to',$empId);
            })->where('id',$id)->first();
        }
        if (!$row && !(Auth::user()->isCompanyManager())) {
            Session::flash('success', "Unauthorized attempt.");
            Session::flash('alert-class', 'alert-danger');
            return redirect()->redirect()->back()->withErrors(['msg', 'No record Found']);
        }

        if($row->created_by==Auth::user()->EmployeeId() || Auth::user()->isCompanyManager()){
          $this->validate($request,[
            'type' => 'required|integer',
            'title' => 'required|max:191|min:3',
            'note' => 'max:500',
            'start_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required',
            'duration' => 'required',
            'priority' => 'required',
            'assigned_to' => 'required',
          ]);
        }else{
          $this->validate($request,[
            'note' => 'max:500',
          ]);
        }

        /**
         * If updated assigned to to another chain user 
         * than send deleted to this chain
         */
        $previous_assigned_to = (int)$row->assigned_to;
        if(Auth::user()->isCompanyManager() || $row->created_by== Auth::user()->EmployeeId()){

          $row->type = $request->type;
          $row->title = $request->title;
          $row->start_datetime = setDateTime($request->start_date . ' ' . date("H:i:s", strtotime($request->start_time)), true);
          $row->duration = $request->duration;
          $row->priority = $request->priority;
          $row->assigned_to = $request->assigned_to;

          if ($request->linkedTo == 'none') {
              $row->client_id = NULL;
          }else{
              $row->client_id = $request->client_id;
          }
          $row->company_id = $company_id;
        }
        $row->note = $request->note;
        if ($request->status) {
            $row->completion_datetime = date('Y-m-d H:i:s');
            $row->completed_by = Auth::user()->EmployeeId();
        }else{
            $row->completion_datetime = Null;
            $row->completed_by = Null;
        }
        $saved = $row->save();

        if($saved){
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $row->assigned_to,
                "data_type" => "activity",
                "data" => "",
                "action_id" => $row->id,
                "title" => "Activity has been updated",
                "description" => $row->title,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $row->company_name = getObjectValue($row->client,"company_name","N/A");
            $row->priority_name = getObjectValue($row->activityPriority,"name","");
            $row->type_name = getObjectValue($row->activityType,"name","");
            $row->created_by_name = Employee::where('id',$row->created_by)->first()->name;
            $row->assigned_to_name = Employee::where('id',$row->assigned_to)->first()->name;

            $assigned_to_superiors = Employee::employeeParents((int)$row->assigned_to, array());
            $created_by_superiors = $row->assigned_to==$row->created_by?array():Employee::employeeParents((int)$row->created_by, array());

            $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));

            $assigned_to_exists = true;
            if($previous_assigned_to !=  $row->assigned_to){
              $assigned_to_exists = false;
            }

            $mergedSuperiorsTokens = Employee::where('company_id', $company_id)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();
            
            $dataPayload = array("data_type" => "activity", "activity" => $row, "action" => "update");
            
            if(!$assigned_to_exists){
              $previous_chain = Employee::employeeSeniors($previous_assigned_to, array());              
              $notf_to_send_ids = array_diff($previous_chain, $merged_superiors);
              $current_similarity = array_intersect($previous_chain, $merged_superiors);
              $previousChainSuperiorsTokens = Employee::where('company_id', $company_id)->whereIn('id', $notf_to_send_ids)->whereNotNull('firebase_token')->pluck('firebase_token')->toArray();
              $payload = array("data_type" => "activity", "activity" => $row, "action" => "delete");
              sendPushNotification_($previousChainSuperiorsTokens, 17, null, $payload);
              /***
               * Send add to current chain
               * $assigned_to_superiors
              */
              $payloadForCurreentChain = array("data_type" => "activity", "activity" => $row, "action" => "add");
              $currentCreatedByToken = array();
              $currentAssignedToToken = array();
              $currentFBToken = array();

              foreach($mergedSuperiorsTokens as $empId=>$token){
                if((int)$empId==(int)$row->created_by && $row->assigned_to!=$row->created_by){
                  array_push($currentCreatedByToken, $token);
                }elseif((int)$empId==(int)$row->assigned_to){
                  array_push($currentAssignedToToken, $token);
                }else{
                  array_push($currentFBToken, $token);
                }
                unset($mergedSuperiorsTokens[$empId]);
              }
              if(!empty($currentAssignedToToken)){
                $notData = $notificationData;
                $notData["title"] = "A new activity has been assigned to you";
                $checkAdmin = Employee::find($row->assigned_to);
                if($checkAdmin->is_admin!=1){
                  if(in_array($row->assigned_to, $created_by_superiors)) sendPushNotification_($currentAssignedToToken, 17, $notData, $dataPayload);
                  else sendPushNotification_($currentAssignedToToken, 17, $notData, $payloadForCurreentChain);
                  // sendPushNotification_($currentAssignedToToken, 17, $notData, $payloadForCurreentChain);
                } 
              }

              if(!empty($currentCreatedByToken)) sendPushNotification_($currentCreatedByToken, 17, $notificationData, $dataPayload);

              $mergedSuperiorsAdminTokens = Employee::where('company_id', $company_id)->whereIn('id', $merged_superiors)->where('is_admin', 1)->orWhereIn('id', $current_similarity)->pluck('firebase_token')->toArray();
              sendPushNotification_($mergedSuperiorsAdminTokens, 17, null, $dataPayload);
              $currentFBToken = array_diff($currentFBToken, $mergedSuperiorsAdminTokens);

              if(!empty($currentFBToken)) sendPushNotification_($currentFBToken, 17, null, $payloadForCurreentChain);
            }

            if(!empty($mergedSuperiorsTokens)) {
              if(array_key_exists($row->assigned_to, $mergedSuperiorsTokens)){
                $assigned_to_token = $mergedSuperiorsTokens[$row->assigned_to];
                unset($mergedSuperiorsTokens[$row->assigned_to]);
              }

              if(array_key_exists($row->created_by, $mergedSuperiorsTokens) && $row->created_by !=$row->assigned_to){
                $created_by_token = $mergedSuperiorsTokens[$row->created_by];
                unset($mergedSuperiorsTokens[$row->created_by]);
                sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
              }
  
              $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
              
              if(!empty($assigned_to_token)) $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
            }
        }

        Session::flash('success', 'Activity updated successfully.');
        Session::flash('alert-class', 'alert-success');
        return response()->json(['message' => 'Activity updated successfully.', 'url' => domain_route('company.admin.activities.index')], 200);
    }//end update

    public function destroy(Request $request, $domain, $id)
    {
        $company_id = config('settings.company_id');
        $empId = Auth::user()->EmployeeId();
        if(Auth::user()->isCompanyManager()){
          $activity = Activity::where('company_id',$company_id)->where('id',$id)->first();
        }else{
          $activity = Activity::where('company_id',$company_id)->where(function($q)use($empId){
            $q = $q->where('created_by',$empId);
          })->where('id',$id)->first();
        }
        if(!$activity && !(Auth::user()->isCompanyManager())) {
            session()->flash("alert","This activity doesn't exist..");
            return back();
        }else{
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $activity->assigned_to,
                "data_type" => "activity",
                "data" => "",
                "action_id" => $activity->id,
                "title" => "Your activity has been deleted.",
                "description" => $activity->title,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $activity_instance = $activity; 
            $activity->delete();

            $activity_instance->deleted_by = $empId;

            $assigned_to_superiors = Employee::employeeParents((int)$activity_instance->assigned_to, array());
            $created_by_superiors = $activity_instance->assigned_to==$activity_instance->created_by?array():Employee::employeeParents((int)$activity_instance->created_by, array());

            $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
            $mergedSuperiorsTokens = Employee::where('company_id', $company_id)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();
             
            // $assigned_to_token = $mergedSuperiorsTokens[$activity_instance->assigned_to];
            // unset($mergedSuperiorsTokens[$activity_instance->assigned_to]);
            
            $dataPayload = array("data_type" => "activity", "activity" => $activity_instance, "action" => "delete");

            // if($activity_instance->assigned_to!=$activity_instance->created_by){
            //   $created_by_token = $mergedSuperiorsTokens[$activity_instance->created_by];
            //   unset($mergedSuperiorsTokens[$activity_instance->created_by]);
            //   sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
            // }
            
            // $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
            // $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
            
            $created_by_token = $mergedSuperiorsTokens[$activity_instance->created_by];
            $assigned_to_token = $mergedSuperiorsTokens[$activity_instance->assigned_to];
            unset($mergedSuperiorsTokens[$activity_instance->assigned_to]);   
            if($created_by_token != $assigned_to_token) unset($mergedSuperiorsTokens[$activity_instance->created_by]);   
            
            if(!empty(array_values($mergedSuperiorsTokens))) $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);

            if($activity_instance->assigned_to == Auth::user()->EmployeeId()) {
              sendPushNotification_(array($assigned_to_token), 17, null, $dataPayload);
            }else{
              sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
            }

            if($activity_instance->created_by!=$activity_instance->assigned_to){
              if($activity_instance->created_by == Auth::user()->EmployeeId()) sendPushNotification_(array($created_by_token), 17, null, $dataPayload);
              else sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
            }

            session()->flash('success','Activity has been deleted.');
            if($request->has('return_url')){
              return redirect()->to($request->return_url);  
            }
            return back();
        }
    }//end destroy

    public function getCommonParties($domain, Request $request, $type=null)
    {
      $company_id = config('settings.company_id');
      //Pluck all clients ids of assignor in case of admin or user
      $isCreatorAdmin = Employee::where('company_id',$company_id)->where('id',$request->created_by)->where('is_admin',1)->first();
      if($isCreatorAdmin){
        $assignerHandles =  DB::table('handles')->where('company_id',$company_id)->pluck('client_id')->toArray('client_id');
      }else{
        $assignerHandles =  DB::table('handles')->where('company_id',$company_id)->where('employee_id',$request->created_by)->pluck('client_id')->toArray('client_id');
      }

      //New concept implementation for displaying assignor parties
      if($type==null){
        $data = Client::select('clients.id','clients.company_name')->where('status','Active')->whereIn('id',$assignerHandles)->get()->toArray();
      }else{
        $data = Client::select('clients.id')->where('status','Active')->whereIn('id',$assignerHandles)->get()->toArray();
      }
      return $data;
      
      //calculations for common parties
      // Pluck all clients ids of assignee in case of admin or user
      // $isAssigneeAdmin = Employee::where('company_id',$company_id)->where('id',$request->employee_id)->where('is_admin',1)->first();
      // if($isAssigneeAdmin){
      //   $assigneeHandles =  DB::table('handles')->pluck('client_id')->toArray('client_id');
      // }else{
      //   $assigneeHandles =  DB::table('handles')->where('employee_id',$request->employee_id)->pluck('client_id')->toArray('client_id');
      // }
      // $commonHandles = array_intersect($assigneeHandles, $assignerHandles);
      // if($type==null){
      //   $data = Client::select('clients.id','clients.company_name')->whereIn('id',$commonHandles)->get()->toArray();
      // }else{
      //   $data = Client::select('clients.id')->whereIn('id',$commonHandles)->get()->toArray();
      // }
    }

    private function getSuperiorsTokens($createdBy,$assignedTo){
      $createdByEmployeeSuperiors = [];
      $assignedToEmployeeSuperiors = [];
      $createdByEmployeeSuperiors = $this->getAllEmployeeSuperior($createdBy,$createdByEmployeeSuperiors);
      $assignedToEmployeeSuperiors = $this->getAllEmployeeSuperior($assignedTo,$createdByEmployeeSuperiors);
      $mergedSuperiors = array_unique(array_merge($createdByEmployeeSuperiors,$assignedToEmployeeSuperiors));
      array_push($mergedSuperiors,$createdBy,$assignedTo);
      $tokens = Employee::whereIn('id',$mergedSuperiors)->whereNotNull('firebase_token')->pluck('firebase_token')->toArray();
      return $tokens;
    }

    private function getAllEmployeeSuperior($empId, $superiors){
        $company_id = Auth::user()->company_id;
        $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
        if(!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))){
          $superiors[] = $getSuperior->superior;
          $superiors = $this->getAllEmployeeSuperior($getSuperior->superior, $superiors);
        }
        return $superiors;
    }

}//end ActivityController