<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Session;
use App\Employee;
Use Auth;
use App\EmployeeGroup;
use Barryvdh\DomPDF\Facade as PDF; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeGroupController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */


  public function index()
  {
    if(Auth::user()->can('settings-view')|| Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
      $company_id = config('settings.company_id');
      $employeegroups = EmployeeGroup::where('company_id', $company_id)
          ->orderBy('created_at', 'desc')
          ->get();
      return view('company.employeegroups.index', compact('employeegroups'));
    }   
    return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
  }

  public function ajaxDatatable(Request $request)
  {
    $columns = array( 
      0 =>'id', 
      1 =>'name',
      2=> 'description',
      3=> 'status',
      4=> 'action',
    );
    $company_id = config('settings.company_id');
    $start = $request->input('start');
    $limit = $request->input('length');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    $prepQuery = EmployeeGroup::where('company_id', $company_id);

    $totalData =  $prepQuery->count();
    $totalFiltered = $totalData; 
    
    $data = array();
    
    if(empty($request->input('search.value'))){
        
      $totalFiltered = $prepQuery->count();
      $employeeGroups = $prepQuery
              ->offset($start)
              ->limit($limit)
              ->orderBy($order,$dir)
              ->get(['id', 'name', 'description', 'status']);
    }elseif(!(empty($request->input('search.value')))) {

      $search = $request->input('search.value'); 

      $employeeGroupsSearchQuery = $prepQuery
                      ->where(function($query) use ($search){
                          $query->orWhere('name','LIKE',"%{$search}%");
                          $query->orWhere('description','LIKE',"%{$search}%");
                          $query->orWhere('status','LIKE',"%{$search}%");
                      });
      $totalFiltered = $employeeGroupsSearchQuery->count();
      $employeeGroups =   $employeeGroupsSearchQuery
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get(['id','name', 'description', 'status']);
    }

    if(!empty($employeeGroups)){   
      $i = $start;
      $getEmployeesGroups = Employee::where('company_id', $company_id)->distinct('employeegroup')->pluck('employeegroup')->toArray();
      foreach ($employeeGroups as $key => $employeeGroup){
          $id = $employeeGroup->id;
          $status = $employeeGroup->status;
          $edit = domain_route('company.admin.employeegroup.edit',[$id]);
          $delete = domain_route('company.admin.employeegroup.destroy',[$id]);

          $nestedData['id'] = ++$i;
          $nestedData['name'] = $employeeGroup->name;
          $nestedData['description'] = strip_tags($employeeGroup->description);
          
          if($status =='Active')
            $spanTag = "<span class='label label-success'>$status</span>"; 
          elseif($status =='Inactive')
            $spanTag = "<span class='label label-danger'>$status</span>";
          $nestedData['status'] = "<a href='#' class='edit-modal' data-id='$id' data-status='$status'>{$spanTag}</a>"; 
          
          if(!(in_array($id, $getEmployeesGroups))){
            $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='$id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>"; 
          }else{
            $deleteBtn = null;
          }
          $nestedData['action'] = "<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>".$deleteBtn;

          $data[] = $nestedData;
      }
    }

    $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
    );

    return json_encode($json_data); 
  }

  public function custompdfdexport(Request $request){
    $getExportData = json_decode($request->exportedData)->data;
    $pageTitle = $request->pageTitle;
    $columns = json_decode($request->columns);
    $properties = json_decode($request->properties);
    set_time_limit ( 300 );
    $pdf = PDF::loadView('company.employeegroups.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
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
    if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
      return view('company.employeegroups.create');
    }
        
    return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      $customMessages = [
          'name.unique' => 'The Employee Group Name already exists',
          'name.required' => 'The Employee Group Name is required'
      ];

      $company_id = config('settings.company_id');
      $this->validate($request, [
          'name' => 'required|unique:employeegroups,name,' . $request->get('name') . ',id,company_id,' . $company_id . ',deleted_at,NULL'

      ], $customMessages);

      $employeegroup = new \App\EmployeeGroup;
      $employeegroup->company_id = $company_id;
      $employeegroup->name = $request->get('name');
      $employeegroup->description = $request->get('description');
      $employeegroup->status = $request->get('status');

      $employeegroup->save();
      return redirect()->route('company.admin.employeegroup', ['domain' => domain()])->with('success', 'Information has been  Added');
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request)
  {
      if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
      $company_id = config('settings.company_id');
      $employeegroup = EmployeeGroup::where('id', $request->id)->where('company_id', $company_id)->first();
      if ($employeegroup)
          return view('company.employeegroups.edit', compact('employeegroup'));
      else
          return redirect()->route('company.admin.employeegroup', ['domain' => domain()]);
      }
          
      return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
      $company_id = config('settings.company_id');
      $employeegroup = EmployeeGroup::findOrFail($request->id);

      // checking number of active users exceeded
      if ($employeegroup->status == 'Inactive' && $request->status == 'Active') {
          $comp_plan = getCompanyPlan($company_id);
          $comp_plan->users;
          $empgroupmembercount = Employee::where('company_id', $company_id)->where('employeegroup', $employeegroup->id)->where('status', 'Inactive')->count();
          $employeescount = Employee::where('company_id', $company_id)->where('status', 'Active')->count();
          if (($employeescount + $empgroupmembercount) > $comp_plan->users) {
              Session::flash('message', 'This Group cannot be activated. Number of users exceed the number of employees allowed in current plan');
              return redirect()->route('company.admin.employeegroup', ['domain' => domain()]);
          }
      }

      $customMessages = [
          'name.unique' => 'The Employee Group Name already exists',
          'name.required' => 'The Employee Group Name is required'
      ];

      $this->validate($request, [
          'name' => 'required|unique:employeegroups,name,' . $request->id . ',id,company_id,' . $company_id,
      ], $customMessages);

      $employeegroup->name = $request->get('name');
      $employeegroup->description = $request->get('description');
      $employeegroup->status = $request->get('status');

      $saved = $employeegroup->save();

      if ($saved && $employeegroup->status == "Inactive") {
          $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->where('employeegroup', $employeegroup->id)->get();
          foreach ($employees as $employee) {
              $employee->status = "Inactive";
              if (!empty($employee->firebase_token)) {
                  //$dataPayload = array("data_type"=>"Logout","employee"=>$employee);
                  //$msgSent = sendPushNotification(null,$employee->firebase_token,$dataPayload,true);
                  $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
              }
              $employee->save();
          }
      } elseif ($saved && $employeegroup->status == "Active") {
          $employees = Employee::where('company_id', $company_id)->where('status', 'Inactive')->where('employeegroup', $employeegroup->id)->get();
          foreach ($employees as $employee) {
              $employee->status = "Active";
              $employee->save();
          }
      }
      return redirect()->route('company.admin.employeegroup', ['domain' => domain()])->with('success', 'Information has been  Updated');
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {
      $company_id = config('settings.company_id');
      $employeegroup = EmployeeGroup::findOrFail($request->id);

      if (!empty($employeegroup) && $employeegroup->name !="Default") {
          $employeeExists = Employee::where('employeegroup', $request->id)->where('company_id', $company_id)->first();
          if($employeeExists){
            session()->flash('message','Sorry! can not remove this employeegroup because this employee group contains employee.');
            return redirect()->back();
          }
          $employeegroup->delete();
      }
      flash()->success('Employee group has been deleted.');
      return back();
  }

  public function changeStatus(Request $request)
  {
      $company_id = config('settings.company_id');
      $employeegroup = EmployeeGroup::findOrFail($request->employeegroup_id);

      // checking number of active users exceeded
      if ($employeegroup->status == 'Inactive' && $request->status == 'Active') {
          $comp_plan = getCompanyPlan($company_id);
          $comp_plan->users;
          $empgroupmembercount = Employee::where('company_id', $company_id)->where('employeegroup', $employeegroup->id)->where('status', 'Inactive')->count();
          $employeescount = Employee::where('company_id', $company_id)->where('status', 'Active')->count();
          if (($employeescount + $empgroupmembercount) > $comp_plan->users) {
              Session::flash('message', 'This Group cannot be activated. Number of users exceed the number of employees allowed in current plan');
              return redirect()->route('company.admin.employeegroup', ['domain' => domain()]);
          }
      }

      $employeegroup->status = $request->status;
      $saved = $employeegroup->save();

      if ($saved && $employeegroup->status == "Inactive") {
          $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->where('employeegroup', $employeegroup->id)->get();
          foreach ($employees as $employee) {
              $employee->status = "Inactive";
              if (!empty($employee->firebase_token)) {
                  $dataPayload = array("data_type" => "Logout", "employee" => $employee);
                  //$msgSent = sendPushNotification(null,$employee->firebase_token,$dataPayload,true);
                  $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
              }
              $employee->save();
          }
      } elseif ($saved && $employeegroup->status == "Active") {
          $employees = Employee::where('company_id', $company_id)->where('status', 'Inactive')->where('employeegroup', $employeegroup->id)->get();
          foreach ($employees as $employee) {
              $employee->status = "Active";
              $employee->save();
          }
      }

      return back();
  }
}
