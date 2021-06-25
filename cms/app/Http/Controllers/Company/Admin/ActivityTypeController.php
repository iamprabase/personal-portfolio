<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ActivityType;
use Auth;
use View;
use DB;
use Barryvdh\DomPDF\Facade as PDF;

class ActivityTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
            $company_id = config('settings.company_id');
            $activityTypes = ActivityType::where('company_id', $company_id)->orderBy('name', 'ASC')->withTrashed()->get();
            return view('company.activity-type.index', compact('activityTypes'));
        }   
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }

    public function create()
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){

        $data['titile'] = 'Add Activity Type';
        $data['row'] = (object)['name' => ''];
        $data['url'] = url(domain_route('company.admin.activity-type.store'));
        $data['method'] = 'POST';
        return view('company.admin.activity-type.create', $data);
        }
            
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }//end create

    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        $this->validate($request, [
            'name' => 'required'
        ]);
        $input['name'] = $request->name;
        $input['company_id'] = $company_id;
        $existActivityType = ActivityType::where('company_id',$company_id)->where('name',$request->name)->first();
        if($existActivityType!=null){
            $data['result'] = false;
        }else{
            $activityType = ActivityType::create($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_type", "activity_type" => $activityType, "action" => "add");
            $sent = sendPushNotification_($fbIDs, 15,null, $dataPayload);

            $data = [];
            $data['activityType'] = ActivityType::where('company_id', $company_id)->orderBy('name', 'ASC')->get();
            foreach ($data['activityType'] as $activityType) {
                $typeCount = $activityType->activities->count();
                if($typeCount==0){
                    $data['del_action'][]='<a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="'.$activityType->id.'"
                       activity-name="'.$activityType->name.'" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                }else{
                    $data['del_action'][]='';
                }
            }
            $data['count'] = ActivityType::where('company_id', $input['company_id'])->get()->count();
            $data['result'] = true;            
        }
        return $data;
    }//end store

    public function edit($id)
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){

        $row = ActivityType::find($id);
        $company_id = config('settings.company_id');
        if (!$row)
            if ($row->company_id != $company_id || !$row) {
                return response()->json(['error' => "This activity priority doesn't exist", 'url' => domain_route('company.admin.activities-type.index')], 422);

            }
        $data['row'] = $row;
        $data['titile'] = 'Edit Activity Type';
        $data['url'] = url(domain_route('activity-type.update', [$row->id]));
        $data['method'] = 'PUT';
        return view('company.admin.activity-type.create', $data);
        }
            
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }//end edit

    public function updateActivityType(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
        ]);
        $company_id = config('settings.company_id');
        $row = ActivityType::where('company_id',$company_id)->where('id',$request->id)->first();
        $rowExists = ActivityType::where('company_id',$company_id)->where('name',$request->name)->where('id','!=',$request->id)->first();
        if($rowExists!=null){
            $data['result'] = false;
        }else{
            $input = $request->all();
            $input['company_id'] = config('settings.company_id');
            $row->update($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_type", "activity_type" => $row, "action" => "update");
            $sent = sendPushNotification_($fbIDs, 15,null, $dataPayload);


            $data = [];
            $data['activityType'] = ActivityType::where('company_id',$company_id)->orderBy('name', 'ASC')->get();
            foreach ($data['activityType'] as $activityType) {
                $typeCount = $activityType->activities->count();
                if($typeCount==0){
                    $data['del_action'][]='<a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="'.$activityType->id.'"
                       activity-name="'.$activityType->name.'" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                }else{
                    $data['del_action'][]='';
                }
            }
            $data['count'] = ActivityType::where('company_id', $input['company_id'])->get()->count();   
            $data['result'] = true;        
        }
        return $data;

    }//end update

    public function deleteActivityType(Request $request)
    {
        $row = ActivityType::where('id',$request->id)->withTrashed()->first();
        $company_id = config('settings.company_id');
        if (!$row || $row->company_id != $company_id || $row->activities->count()!=0 ) {
            $data['result']=false;
        }else {
            $row->forceDelete();

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_type", "activity_type" => $row, "action" => "delete");
            $sent = sendPushNotification_($fbIDs, 15,null, $dataPayload);



            $data = [];
            $data['activityType'] = ActivityType::where('company_id',$company_id)->orderBy('name', 'ASC')->get();
            foreach ($data['activityType'] as $activityType) {
                $typeCount = $activityType->activities->count();
                if($typeCount==0){
                    $data['del_action'][]='<a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="'.$activityType->id.'"
                       activity-name="'.$activityType->name.'" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                }else{
                    $data['del_action'][]='';
                }
            }
            $data['count'] = ActivityType::where('company_id', $company_id)->get()->count();
            $data['result']=true;
        }
        return $data;
    }//end destroy

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData);
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.activity-type.exportpdf', compact('getExportData', 'pageTitle', 'columns',
      'properties'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }
}
