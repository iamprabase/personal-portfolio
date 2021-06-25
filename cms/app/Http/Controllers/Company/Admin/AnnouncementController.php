<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Announcement;
use App\User;
use App\Employee;
use App\EmployeeGroup;
use Auth;
use Log;
use DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:announcement-create', ['only' => ['create','store']]);
        $this->middleware('permission:announcement-view');
        $this->middleware('permission:announcement-update', ['only' => ['edit','update']]);
        $this->middleware('permission:announcement-delete', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $announcements = Announcement::where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $announcementCount = $announcements->count();
        return view('company.announcements.index', compact('announcements','announcementCount'));
    }

    public function ajaxTable(Request $request){
        $company_id = config('settings.company_id');
        $columns = array( 
            0 =>'id', 
            1 =>'title',
            2 =>'description',
            3 =>'created_at',
            4=> 'status',
            5=> 'action',
        );
        $totalData = Announcement::where('company_id',$company_id)->get()->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if($request->startDate){
          $startDateFilter = $request->input('startDate');
        }
        if($request->endDate){
          $endDateFilter = $request->input('endDate');
          $endDateFilter = Carbon::parse($endDateFilter)->addDays(1)->format('Y-m-d');
        }
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

        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        $announcements =  Announcement::where('company_id',$company_id);
                  if($request->startDate){
                    $announcements = $announcements->where('announcements.created_at','>=',$startDateFilter);
                  }
                  if($request->endDate){
                    $announcements = $announcements->where('announcements.created_at','<=',$endDateFilter);
                  }
                  if($request->input('search.value')){
                    $announcements = $announcements->where('announcements.id','LIKE',"%{$search}%")
                    ->orWhere('announcements.title', 'LIKE',"%{$search}%")
                    ->orWhere('announcements.created_at', 'LIKE',"%{$search}%")
                    ->orWhere('announcements.description', 'LIKE',"%{$search}%")
                    ->orWhere('announcements.status', 'LIKE',"%{$search}%");
                  }
                  
                  $totalFiltered = $announcements->get()->count();
                  $announcements = $announcements->orderBy($order,$dir)->offset($start)
                              ->limit($limit)
                              ->get();
        $data = array();
        if(!empty($announcements))
        {   
            $i = $start;
            foreach ($announcements as $announcement)
            {
                $show =  domain_route('company.admin.announcement.show',[$announcement->id]);
                $edit =  domain_route('company.admin.announcement.edit',[$announcement->id]);
                $nestedData['id'] = ++$i;
                
                $nestedData['title'] = $announcement->title;
                $nestedData['description'] = $announcement->description;
                $nestedData['created_at'] = getDeltaDate(Carbon::parse($announcement->created_at)->format('Y-m-d'));
                $nestedData['status'] = '<span class="label label-success">Active</span>';
                $nestedData['action']='<a href="#" class="edit-modal btn btn-success btn-sm" data-id="'.$announcement->id.'" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>';
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
      $pdf = PDF::loadView('company.announcements.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
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
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        }else{
          return redirect()->back();
        }
        if(Auth::user()->isCompanyManager()){
            $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'ASC')->select("name", "id")->get();
            $empId = 0;
            $employees = Employee::select("employees.name as employeeName", "employees.id as employeeID","employees.designation","designations.id as designationID","designations.name as designationName","employees.company_id")->
            leftJoin('designations','employees.designation','designations.id')
            ->orderBy('designationID', 'ASC')->where('employees.company_id',$company_id)->where('status','Active')->get();
            $empData=[];
            foreach($employees as $employee){
                  if($employee->designationName==""){
                    $employee->designationName = "No Designation";
                  }
                  $empData[$employee->designationName][$employee->employeeID]['id']= $employee->employeeID;
                  $empData[$employee->designationName][$employee->employeeID]['emp_name']=$employee->employeeName; 
            }
        }else{      
            $empId = Auth::user()->EmployeeId();      
            $getChainUsers = Auth::user()->getAllChainUsers($empId);
            $employees = Employee::select("employees.name as employeeName", "employees.id as employeeID","employees.designation","designations.id as designationID","designations.name as designationName","employees.company_id")->
            leftJoin('designations','employees.designation','designations.id')
            ->whereIn('employees.id',$getChainUsers)->where('employees.company_id',$company_id)->orderBy('designationID', 'ASC')->where('status','Active')->get();
            $empData=[];
            foreach($employees as $employee){
              if($employee->designationName==""){
                   $employee->designationName = "No Designation";
              }
              $empData[$employee->designationName][$employee->employeeID]['id']= $employee->employeeID;
              $empData[$employee->designationName][$employee->employeeID]['emp_name']=$employee->employeeName;                
            }
        }
        $employee_groups = EmployeeGroup::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        return view('company.announcements.create', compact('employees', 'employee_groups','empData','empId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */




    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $company_id = config('settings.company_id');
        $announcement = new \App\Announcement;
        $announcement->company_id = $company_id;
        $announcement->title = $request->get('title');
        $announcement->description = $request->get('description');
        $announcement->status = $request->get('status');
        $saved = $announcement->save();
        if ($saved) {
            if ($request->get('employees')) {
                foreach ($request->employees as $key => $value) {
                    DB::table('announce_employee')->insert([
                            'company_id' => $company_id,
                            'announcement_id' => $announcement->id,
                            'employee_id' => $value,
                    ]);
                }
                if(Auth::user()->isCompanyEmployee()){
                    DB::table('announce_employee')->insert([
                            'company_id' => $company_id,
                            'announcement_id' => $announcement->id,
                            'employee_id' =>  Auth::user()->EmployeeId(),
                    ]);
                }
                $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereIn('id', $request->get('employees'))->whereNotNull('firebase_token')->pluck('firebase_token', 'id');
                if (!empty($fbIDs)) {
                    $notificationData = array(
                        'company_id' => $company_id,
                        'employee_id' => null,
                        'title' => $request->get('title'),
                        'description' => $request->get('description'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'to' => 1
                    );
                    $sendingNotificationData = $notificationData;
                    $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client 
                    foreach($fbIDs as $eId=>$fbID){
                      $fbIDArray = array($fbID);
                      $sent = sendPushNotification_($fbIDArray, 6, $sendingNotificationData, null);
                      $sentStatus = json_decode($sent);
                      if($sentStatus->failure == 1){
                        DB::table('unsent_announcement')->insert(['employee_id'=> $eId, 'announcement_id'=> $announcement->id]);
                      }
                    }
                }
            } 
        }
        return redirect()->route('company.admin.announcement', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $announcement = Announcement::where('id', $request->id)->where('company_id', $company_id)->first();
        $employees = DB::table('announce_employee')->select('employee_id')->where('company_id', $company_id)->where('announcement_id', $request->id)->get();
        if ($announcement)
            return view('company.announcements.show', compact('announcement', 'employees'));
        else
            return redirect()->route('company.admin.announcement', ['domain' => domain()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $announcement = Announcement::where('id', $request->id)->where('company_id', $company_id)->first();
        $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        $previous_list = DB::table('announce_employee')->select('employee_id')->where('company_id', $company_id)->where('announcement_id', $request->id)->get();
        if ($announcement)
            return view('company.announcements.edit', compact('announcement', 'employees', 'previous_list'));
        else
            return redirect()->route('company.admin.announcement', ['domain' => domain()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);
        $company_id = config('settings.company_id');
        $announcement = Announcement::findOrFail($request->id);
        $announcement->title = $request->get('title');
        $announcement->description = $request->get('description');
        $announcement->status = $request->get('status');

        if ($announcement->save()) {
            if ($request->get('employee_id')) {
                foreach ($request->get('employee_id') as $employee_id) {
                    DB::table('announce_employee')->insert([
                        'company_id' => $company_id,
                        'announcement_id' => $announcement->id,
                        'employee_id' => $employee_id
                    ]);
                }
            }
            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereIn('id', $request->get('employee_id'))->whereNotNull('firebase_token')->pluck('firebase_token');

            if (!empty($fbIDs)) {

                $notificationData = array(
                    'company_id' => $company_id,
                    'employee_id' => null,
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'to' => 1
                );

                $sendingNotificationData = $notificationData;
                $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client time

                //save notification 
                //$nSaved = DB::table('notifications')->insertGetId($notificationData);
                if (true) {  //currently skiping save in database
                    $msgID = sendPushNotification($sendingNotificationData, $fbIDs);

                }

            }
        }

        return redirect()->route('company.admin.announcement', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $announcement = Announcement::findOrFail($request->id);
        $announcement->delete();
        $announce_employee = DB::table('announce_employee')->where('announcement_id', $request->id)->get();
        foreach ($announce_employee as $announced) {
            $announced->delete();
        }
        flash()->success('Announcement has been deleted.');
        return back();
    }

    public function detail($domain,Request $request){
        $company_id = config('settings.company_id');
        $data['result']=false;
        $announcement = Announcement::where('company_id',$company_id)->with('employees')->where('id',$request->id)->first();
        if($announcement){
            $announcement->date = getDeltaDate($announcement->created_at->format('Y-m-d')).' '.$announcement->created_at->format('g:i A');
        }
        $data['announcement'] = $announcement;
        if($data['announcement']){
            $data['result']=true;
        }
        return response()->json($data);
    }

}
