<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Leave;
use App\Employee;
use App\LeaveType;
use App\User;
Use Auth;
use DB;
use Session;
use Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:leave-create', ['only' => ['create','store']]);
        $this->middleware('permission:leave-view');
        $this->middleware('permission:leave-update', ['only' => ['edit','update']]);
        $this->middleware('permission:leave-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $leaves = Auth::user()->handleQuery('leave')
            ->orderBy('created_at', 'desc')
            ->get();
        $employee_ids = $leaves->unique('employee_id')
                            ->pluck('employee_id')
                            ->toArray();
        $employees =Auth::user()->handleQuery('employee')
                                    ->whereIn('id', $employee_ids)
                                    ->orderBy('name','asc')
                                    ->pluck('name','id')
                                    ->toArray();
        $leavesCount = $leaves->count();
        $nCal = config('settings.ncal');
        return view('company.leaves.index', compact('leaves','leavesCount','employees', 'nCal'));
    }

    public function ajaxTable(Request $request){
        $columns = array( 
            0 =>'id', 
            1 =>'start_date',
            2 =>'end_date',
            3 => 'noDays',
            4 => 'LeaveTypeName',
            5 => 'remarks',
            6 => 'AddedByName',
            7 => 'ApprovedByName',
            8 => 'status',
            9 => 'action',
        );
        $totalData = Auth::user()->handleQuery('leave')->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if($request->empVal){
          $employee_id = $request->empVal;
        }
        if($request->status){
          $status = $request->status;
        }
        if($request->startDate){
          $startDateFilter = $request->input('startDate');
        }
        if($request->endDate){
          $endDateFilter = $request->input('endDate');
        }
        $empId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
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

        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        $leaves =  Auth::user()->handleQuery('leave')
                  ->leftJoin('employees as addedBy_tbl','leaves.employee_id','addedBy_tbl.id')
                  ->leftJoin('employees as approvedBy_tbl','leaves.approved_by','approvedBy_tbl.id')
                  ->leftJoin('leave_type','leaves.leavetype','leave_type.id')
                  ->select('leaves.*','addedBy_tbl.id as AddedById','addedBy_tbl.name as AddedByName','approvedBy_tbl.id as ApprovedById','approvedBy_tbl.name as ApprovedByName','leave_type.id as leaveTypeId','leave_type.name as LeaveTypeName')->selectRaw('DATEDIFF(leaves.end_date,leaves.start_date)+1 AS noDays');
                  if($request->empVal){
                    $leaves = $leaves->where('employee_id',$employee_id);
                  }
                  if($request->status){
                    $leaves = $leaves->where('leaves.status',$status);
                  }
                  if($request->startDate){
                    $leaves = $leaves->where('leaves.start_date','>=',$startDateFilter);
                  }
                  if($request->endDate){
                    $leaves = $leaves->where('leaves.end_date','<=',$endDateFilter);
                  }
                  if($request->input('search.value')){
                    $leaves = $leaves->where('leaves.id','LIKE',"%{$search}%")
                    ->orWhere('leaves.start_date', 'LIKE',"%{$search}%")
                    ->orWhere('leaves.end_date', 'LIKE',"%{$search}%")
                    ->orWhere(\DB::raw('DATEDIFF(leaves.end_date,leaves.start_date)+1'), 'LIKE',"%{$search}%")
                    ->orWhere('addedBy_tbl.name', 'LIKE',"%{$search}%")
                    ->orWhere('approvedBy_tbl.name', 'LIKE',"%{$search}%")
                    ->orWhere('leave_type.name', 'LIKE',"%{$search}%")
                    ->orWhere('leaves.remarks', 'LIKE',"%{$search}%")
                    ->orWhere('leaves.status', 'LIKE',"%{$search}%");
                  }
                  
                  $totalFiltered = $leaves->get()->count();
                  $leaves = $leaves->orderBy($order,$dir)->offset($start)
                              ->limit($limit)
                              ->get();
        $data = array();
        if(!empty($leaves))
        {   
            $i = $start;
            foreach ($leaves as $leave)
            {
                $show =  domain_route('company.admin.leave.show',[$leave->id]);
                $edit =  domain_route('company.admin.leave.edit',[$leave->id]);
                $delete =  domain_route('company.admin.leave.destroy',[$leave->id]);
                $nestedData['id'] = ++$i;
                $nestedData['start_date'] = getDeltaDate(Carbon::parse($leave->start_date)->format('Y-m-d'));
                $nestedData['end_date'] = getDeltaDate(Carbon::parse($leave->end_date)->format('Y-m-d'));
                $nestedData['noDays'] = $leave->noDays;
                $nestedData['LeaveTypeName'] = $leave->LeaveTypeName;
                $nestedData['remarks'] = $leave->leave_desc;

                if(isset($leave->employee_id) && isset($leave->employee->name)){
                    $nestedData['AddedByName'] = '<a href="'.domain_route('company.admin.employee.show',[$leave->employee_id]).'" datasalesman="'. $leave->AddedByName .'">'. $leave->AddedByName.'</a>';
                }else{
                    $nestedData['AddedByName'] = '';
                }

                if(isset($leave->approved_by)){
                    if(in_array($leave->approved_by,$allSup)){
                        $nestedData['ApprovedByName'] = '<a href="#" class="alert-modal" datasalesman="'. $leave->ApprovedByName .'">'. $leave->ApprovedByName.'</a>';
                    }else{
                        $nestedData['ApprovedByName'] = '<a href="'.domain_route('company.admin.employee.show',[$leave->approved_by]).'" datasalesman="'. $leave->ApprovedByName .'">'. $leave->ApprovedByName.'</a>';
                    }
                }else{
                    $nestedData['ApprovedByName'] = '';
                }      

                if(Auth::user()->isCompanyManager()){
                    if(Auth::user()->can('leave-status'))
                    $nestedData['status'] = '<a href="#" class="edit-modal" data-id="'.$leave->id.'" data-status="'.$leave->status.'" data-id="'.$leave->id.'"
                       data-status="'.$leave->status.'" data-remark="'.$leave->status_reason.'">';
                    else
                      $nestedData['status'] = '<a href="#" class="alert-modal" data-id="'.$leave->id.'" data-status="'.$leave->status.'" data-id="'.$leave->id.'"
                       data-status="'.$leave->status.'" data-remark="'.$leave->status_reason.'">';

                    if($leave->status =='Approved'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-success">'. $leave->status.'</span>';
                    }elseif($leave->status =='Pending'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-warning">'. $leave->status.'</span>';
                    }else{
                      $nestedData['status'] =$nestedData['status'].'<span class="label label-danger">'. $leave->status.'</span>';
                    }
                    $nestedData['status'] =$nestedData['status'].'</a>';                    
                }else{
                  if(Auth::user()->can('leave-status'))
                  $modalClass = ((getEmployee($leave->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal':'alert-modal';
                  else
                  $modalClass = 'alert-modal';

                  $nestedData['status'] ='<a href="#" class="'.$modalClass.'" data-id="'.$leave->id.'" data-status="'.$leave->status.'" data-remark="'.$leave->remark.'">';

                    if($leave->status =='Approved'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-success">'. $leave->status.'</span>';
                    }elseif($leave->status =='Pending'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-warning">'. $leave->status.'</span>';
                    }else{
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-danger">'. $leave->status.'</span>';
                    }
                  $nestedData['status'] = $nestedData['status'].'</a>';
                }

                if($leave->employee_type=="Admin"){
                  $datatype = 'data-type="Admin';
                }else{
                  $datatype ='';
                }

                $nestedData['action']='<a href="'.$show.'" class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>';
                if(Auth::user()->can('leave-update') && Auth::user()->EmployeeId() == $leave->employee_id && $leave->status =='Pending') $nestedData['action']=$nestedData['action'].'<a href="'.$edit.'" class="btn btn-warning btn-sm" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>';
                if(Auth::user()->can('leave-delete') && Auth::user()->EmployeeId() == $leave->employee_id && $leave->status =='Pending') $nestedData['action']=$nestedData['action'].'<a class="btn btn-danger btn-sm delete" data-mid="'.$leave->id.'" data-url="'.$delete.'" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                
                $nestedData['ea'] = $leave->amount;
                $nestedData['e_date'] = Carbon::parse($leave->created_at)->format('Y-m-d');
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

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.leaves.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        
        $leavetypes = LeaveType::orderBy('name', 'asc')->where('company_id',$company_id)->pluck('name', 'id')->toArray();
        return view('company.leaves.create', compact('leavetypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'leavetype.required'      => 'The Leave Type field is required.',
            'start_date.required'     => 'From Date is required',
            'start_date.date'         => 'Start date should be a valid date',
            'end_date.required'       => 'To Date is required',
            'end_date.date'           => 'End date should be a valid date',
            'end_date.after_or_equal' => 'End date can not be before start date',
            'leave_desc.required'     => 'Leave description is required',
        ];

        $this->validate($request, [
            'leavetype' => 'required',
            'start_date'=> 'required|date_format:Y-m-d',
            'end_date'  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'leave_desc' => 'required',
        ], $customMessages);

        $company_id = config('settings.company_id');
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $leaveExists = Leave::where('company_id',$company_id)->where('employee_id',Auth::user()->EmployeeId())->where(function($q)use($start_date,$end_date){

                $q= $q->where(function($q1)use($start_date,$end_date){
                    $q1 = $q1->where('start_date','<=',$start_date)->where('end_date','>=',$start_date);
                })->orWhere(function($q2)use($start_date,$end_date){
                    $q2=$q2->where('start_date','<=',$end_date)->where('end_date','>=',$end_date);
                })->orWhere(function($q3)use($start_date,$end_date){
                    $q3=$q3->where('start_date','>=',$start_date)->where('end_date','<=',$end_date);
                });
        })->first();

        if($leaveExists){
            session()->flash('alert','Leave exists on this date');
            return redirect()->back();
        }
        
        $leave               = new \App\Leave;
        $leave->company_id   = $company_id;
        $leave->leavetype    = $request->get('leavetype');
        $leave->employee_id  = Auth::user()->EmployeeId();
        $leave->employee_type= "Employee";
        $leave->leave_desc   = $request->leave_desc;

        if(isset($request->englishDate)){
            $leave->start_date = $request->get('englishDate');
        }else{
            $leave->start_date = $request->get('start_date');
        }
        if(isset($request->endenglishDate)){
            $leave->end_date = $request->get('endenglishDate');
        }else{
            $leave->end_date = $request->get('end_date');
        }

        $leave->leave_desc= $request->get('leave_desc');
        $leave->status    = 'Pending';
        $saved            = $leave->save();
        if ($saved) {
            //send notification to specific employee
            $temp = Employee::where('company_id',$company_id)->where('id', Auth::user()->EmployeeId())->first();
            // Log::info('info', array("message leaveEmployee"=>print_r($temp,true)));
            //Log::info('info', array("temp"=>print_r($temp,true)));
            if (!empty($temp)) {
                $notificationData = array(
                    "company_id" => $temp->company_id,
                    "employee_id"=> $temp->id,
                    "data_type"  => "leave",
                    "data"       => "",
                    "action_id"  => $leave->id,
                    "title"      => "Leave ",
                    "description"=> "Your Leave has been Added",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status"     => $leave->status,
                    "to"         => 1
                );

                $sendingNotificationData = $notificationData;
                $sendingNotificationData["unix_timestamp"] = time();

                //save notification

                //$nSaved = DB::table('notifications')->insertGetId($notificationData);

                if (!empty($temp->firebase_token)) {
                    $dataPayload = array("data_type" => "leave", "leave" => $leave, "action" => "add");
                    $msgID = sendPushNotification_([$temp->firebase_token], 2, $sendingNotificationData, $dataPayload);
                }
            }
        }

        return redirect()->route('company.admin.leave', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id= config('settings.company_id');
        $leave =Auth::user()->handleQuery('leave',$request->id)->first();
        $action=NULL;
        if(Auth::user()->can('leave-update') && Auth::user()->EmployeeId() == $leave->employee_id && $leave->status =='Pending'){
          $edit_link = domain_route('company.admin.leave.edit', [$request->id]);
          $action = "<a class='btn btn-warning btn-sm edit' href='{$edit_link}'  style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
        }
        $delete =  domain_route('company.admin.leave.destroy',[$leave->id]);
        if(Auth::user()->can('leave-delete') && Auth::user()->EmployeeId() == $leave->employee_id && $leave->status =='Pending') $action = $action.'<a class="btn btn-danger btn-sm delete" data-mid="'.$leave->id.'" data-url="'.$delete.'" data-toggle="modal" data-target="#delete" style="padding: 7px 6px;"><i class="fa fa-trash-o"></i>Delete</a>';
          
        if ($leave)
            return view('company.leaves.show', compact('leave','action'));
        else
            return redirect()->back()->withErrors(['msg', 'No record Found']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id= config('settings.company_id');
        $leave     = Leave::where('id', $request->id)->first();
        $employees = Auth::user()->handleQuery('employee')->pluck('name', 'id')->toArray();
        $leavetypes= LeaveType::where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        if ($leave){
            return view('company.leaves.edit', compact('leave', 'employees', 'leavetypes'));
        }
        else{
            return redirect()->route('company.admin.leave', ['domain' => domain()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $company_id = config('settings.company_id');
        $leave = Leave::where('company_id',$company_id)->where('id', $request->id)->first();
        $customMessages = [
            'leavetype.required'     => 'The Leave Type field is required.',
            'start_date.required'    => 'From Date is required',
            'end_date.required'      => 'To Date is required',
            'end_date.after_or_equal'=> 'To Date must be after or equal to start date.'
        ];


        $this->validate($request, [
            'leavetype' => 'required',
            'start_date'=> 'required|date_format:Y-m-d',
            'end_date'  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'leave_desc'=> 'required',
        ], $customMessages);


        // 'email' => 'required|email|unique:users,email,' . $id,
        //    'roles' => 'required|min:1'
        //$leave= new \App\Company;
        $leave->leavetype = $request->get('leavetype');

        if(isset($request->englishDate)){
            $leave->start_date = $request->get('englishDate');
        }else{
            $leave->start_date = $request->get('start_date');
        }
        if(isset($request->endenglishDate)){
            $leave->end_date = $request->get('endenglishDate');
        }else{
            $leave->end_date = $request->get('end_date');
        }
        $leave->leave_desc = $request->get('leave_desc');

        $leaveExists = Leave::where('company_id',$company_id)->where('id', '!=', $request->id)->where('employee_id',Auth::user()->EmployeeId())->where(function($q)use($leave){

                $q= $q->where(function($q1)use($leave){
                    $q1 = $q1->where('start_date','<=',$leave->start_date)->where('end_date','>=',$leave->start_date);
                })->orWhere(function($q2)use($leave){
                    $q2=$q2->where('start_date','<=',$leave->end_date)->where('end_date','>=',$leave->end_date);
                });
        })->first();

        if($leaveExists){
          session()->flash('alert', 'Leave exists on this date');
          return redirect()->back();
        }


        $saved = $leave->save();

        if ($saved) {
            //send notification to specific employee
            $temp = Employee::where('company_id',$company_id)->where('id',$leave->employee_id)->first();
            // $temp = Auth::user()->handleQuery('employee', $request->get('employee_id'))->first();
            // Log::info('info', array("temp"=>print_r($temp,true)));
            if (!empty($temp)) {
                $notificationData = array(
                    "company_id" => $temp->company_id,
                    "employee_id" => $temp->id,
                    "data_type" => "leave",
                    "data" => "",
                    "action_id" => $leave->id,
                    "title" => "Leave " . $request->get('status'),
                    "description" => "Your Leave has been updated",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => $leave->status,
                    "to" => 1
                );

                $sendingNotificationData = $notificationData;
                $sendingNotificationData["unix_timestamp"] = time();

                //save notification

                //$nSaved = DB::table('notifications')->insertGetId($notificationData);

                if (!empty($temp->firebase_token)) {
                    $dataPayload = array("data_type" => "leave", "leave" => $leave, "action" => "update");
                    $msgID = sendPushNotification_([$temp->firebase_token], 2, $sendingNotificationData, $dataPayload);
                }

            }

        }
        session()->flash('success', 'Leave Updated Successfully');
        Session::flash('DT_Leav_filters', $request->DT_Leav_FILTER);
        $newprevious=explode('/',$request->previous_url);
        if($newprevious[4]=='employee' || $newprevious[4]=='client'){
          $previous = $request->previous_url;
          return redirect($previous);
        }else{
      
          return redirect()->route('company.admin.leave', ['domain' => domain()])->with('success', 'Information has been  Updated');
        }
       // $url = $request->previous_url;
       // return redirect($url);
        // return redirect()->route('company.admin.leave', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $leave = Auth::user()->handleQuery('leave')->where('id',$request->leave_id)->first();
        if($leave){
            $leave->status = $request->status;
            $leave->status_reason = $request->remark;
            if($leave->status=='Approved' || $leave->status=="Rejected"){
              $leave->approved_by = Auth::user()->EmployeeId();
            }
            $saved = $leave->save();
            if (!empty($saved)) {
                //send notification to specific employee
                $temp = Auth::user()->handleQuery('employee', $leave->employee_id)->first();
                //Log::info('info', array("temp" => print_r($temp, true)));
                if (!empty($temp)) {
                    $notificationData = array(
                        "company_id" => $temp->company_id,
                        "employee_id" => $temp->id,
                        "data_type" => "leave",
                        "data" => "",
                        "action_id" => $leave->id,
                        "title" => "Leave " . $request->get('status'),
                        "description" => "Your Leave has been " . $request->get('status'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "status" => 1,
                        "to" => 1
                    );

                    $sendingNotificationData = $notificationData;
                    $sendingNotificationData["unix_timestamp"] = time();
                    if (!empty($temp->firebase_token)) {

                        $dataPayload = array("data_type" => "Leave", "leave" => $leave, "action" => "update_status", "leave_id" => $leave->id, "status" => $leave->status, "remark" => $leave->remarks);
                        $msgID = sendPushNotification_([$temp->firebase_token], 2, $sendingNotificationData, $dataPayload);
                    }
                }

            }
        
        }
        return back();
    }

    public function approve(Request $request)
    {
        $leave = Auth::user()->handleQuery('leave', $request->id)->first();
        $leave->remarks = $request->remark;
        $leave->status = $request->status;
        $leave->save();
        return response()->json($leave);
        // return redirect()->route('company.admin.leave', ['domain' => domain()])->with('success','Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $leave = Leave::findOrFail($request->id);
        $employee_id = $leave->employee_id;
        $deleted = $leave->delete();
        \Session::flash('success', 'Leave has been deleted.');

        $temp = Employee::where('id', $employee_id)->first();
        if($deleted){
          $dataPayload = array("data_type" => "Leave", "leave" => $request->id, "action" => "delete");
          $sent = sendPushNotification_([$temp->firebase_token], 2, null, $dataPayload);
        }
        
        if($request->has('previous_url')) return redirect()->route('company.admin.leave', ['domain' => domain()]);

        else return redirect()->back();
    }
}