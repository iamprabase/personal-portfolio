<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;
use URL;
use Auth;
use Session;
use App\Bank;
use App\User;
use DateTime;
use App\Order;
use Validator;
use App\Client;
use App\Company;
use App\Holiday;
use App\Manager;
use App\NoOrder;
use App\Activity;
use App\Employee;
use App\BeatVPlan;
use App\DayRemark;
use Carbon\Carbon;
use App\Attendance;
use App\Collection;
use App\ClientVisit;
use App\Designation;
use App\LogActivity;
use App\ClientSetting;
use App\EmployeeGroup;
use App\BeatPlansDetails;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    private $company_id;
    private $comp_users;

    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        $this->comp_users = Company::find($this->company_id)->num_users;
        $this->middleware('permission:employee-create', ['only' => ['create','store']]);
        $this->middleware('permission:employee-view');
        $this->middleware('permission:employee-update', ['only' => ['edit','update']]);
        $this->middleware('permission:employee-delete', ['only' => ['destroy']]);
    }


    public function index()
    {
        $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'asc')
        ->get(['name','status', 'role']);

        $employeesCount = $employees->count();
        $employeescount = $employees->filter(
            function ($request) {
            return  $request->status == 'Active';
        }
        )->count();
        $filtered = "";
        $company_id = config('settings.company_id');
        if (Auth::user()->isCompanyEmployee()) {
            $empData = Employee::where('company_id', $company_id)->where('id', Auth::user()->employeeID())->first();
            $subDesignationExists = Designation::where('company_id', $company_id)->where('parent_id', $empData->designation)->first();
        } else {
            $subDesignationExists = true;
        }

        $employeegroups = EmployeeGroup::where('company_id', $company_id)->whereStatus('Active')->orderBy('name', 'asc')->with('employees')->pluck('name', 'id')->toArray();
        $designations = Designation::where('company_id', $company_id)->orderBy('name', 'asc')->with('employees')->pluck('name', 'id')->toArray();
        $roles = DB::table('roles')->where('company_id', $company_id)->pluck('name', 'id')->toArray();

        if ($employeescount >= $this->comp_users) {
            Session::flash('message', 'You have reached maximum no. of user limit.');
        }

        $planUsers = $this->comp_users;
    
        return view('company.employees.index', compact('employees', 'filtered', 'employeesCount', 'employeegroups', 'designations', 'employeescount', 'subDesignationExists', 'planUsers', 'roles'));
    }

    public function filteredDesignation(Request $request)
    {
        $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'asc')
        ->get(['name','status', 'role']);
        $employeesCount = $employees->count();
        $employeescount = $employees->filter(
            function ($request) {
            return  $request->status == 'Active';
        }
        )->count();
        $filtered = $request->filtered;
        $company_id = config('settings.company_id');
        if (Auth::user()->isCompanyEmployee()) {
            $empData = Employee::where('company_id', $company_id)->where('id', Auth::user()->employeeID())->first();
            $subDesignationExists = Designation::where('company_id', $company_id)->where('parent_id', $empData->designation)->first();
        } else {
            $subDesignationExists = true;
        }

        $employeegroups = EmployeeGroup::where('company_id', $company_id)->orderBy('name', 'asc')->with('employees')->pluck('name', 'id')->toArray();
        $designations = Designation::where('company_id', $company_id)->orderBy('name', 'asc')->with('employees')->pluck('name', 'id')->toArray();
        $roles = DB::table('roles')->where('company_id', $company_id)->pluck('name', 'id')->toArray();

        if ($employeescount >= $this->comp_users) {
            Session::flash('message', 'You have reached maximum no. of user limit.');
        }
        $planUsers = $this->comp_users;
        return view('company.employees.index', compact('employees', 'filtered', 'employeesCount', 'employeegroups', 'designations', 'employeescount', 'subDesignationExists', 'planUsers', 'roles'));
    }

    public function ajaxDatatable(Request $request)
    {
        $columns = array(
          0 =>'id',
          1 =>'name',
          2=> 'phone',
          3=> 'email',
          4=> 'employeegroup',
          5=> 'designations',
          6=> 'accessibility',
          7=> 'status',
          8=> 'last_action',
          9=> 'action',
      );

        $totalData =  Auth::user()->handleQuery('employee')->orderBy('created_at', 'desc')
                      ->count();

        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $empFilter = $request->input('empVal');
        $order = $columns[$request->input('order.0.column')];
        if($order== "accessibility") $order = "role";
        $dir = $request->input('order.0.dir');
        $employeegroup = $request->input('employeegroup');
        $designation = $request->input('designation');
        $role = $request->input('role');
        
      
        if ($order=='last_action') {
            if (empty($request->input('search.value'))) {
                $totalFiltered = Auth::user()->handleQuery('employee')->where(function ($query) use ($designation, $employeegroup) {
                    if (isset($designation)) {
                        $query->where('employees.designation', $designation);
                    }
                    if (isset($employeegroup)) {
                        $query->where('employees.employeegroup', $employeegroup);
                    }
                    if (isset($role)) {
                      $query->where('employees.role', $role);
                    }
                })->leftJoin('activity_log', 'activity_log.causer_id', 'employees.user_id')->selectRaw("employees.*, MAX(activity_log.created_at) as last_action")->groupBy('employees.id')
                      ->orderBy($order, $dir)->get()->count();

                $employees =  Auth::user()->handleQuery('employee')->where(function ($query) use ($designation, $employeegroup) {
                    if (isset($designation)) {
                        $query->where('employees.designation', $designation);
                    }
                    if (isset($employeegroup)) {
                        $query->where('employees.employeegroup', $employeegroup);
                    }
                    if (isset($role)) {
                      $query->where('employees.role', $role);
                    }
                })->leftJoin('designations', 'employees.designation', 'designations.id')->leftJoin('activity_log', 'activity_log.causer_id', 'employees.user_id')->selectRaw("employees.*, MAX(activity_log.created_at) as last_action, designations.name as designations")->groupBy('employees.id')
                        ->orderBy($order, $dir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();
            } elseif (!(empty($request->input('search.value')))) {
                $search = $request->input('search.value');

                $allEmployees = Auth::user()->handleQuery('employee')
                            ->leftJoin('employeegroups', 'employees.employeegroup', 'employeegroups.id')->leftJoin('designations', 'employees.designation', 'designations.id')->leftJoin('activity_log', 'activity_log.causer_id', 'employees.user_id')
                            ->where(function ($query) use ($search) {
                                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.phone', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.email', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.status', 'LIKE', "%{$search}%");
                                $query->orWhere('employeegroups.name', 'LIKE', "%{$search}%");
                                $query->orWhere('designations.name', 'LIKE', "%{$search}%");
                            })->selectRaw("employees.*, MAX(activity_log.created_at) as last_action, employeegroups.name as employeegroups, designations.name as designations")->groupBy('employees.id');
                if (isset($employeegroup)) {
                    $allEmployees = $allEmployees->where('employees.employeegroup', $employeegroup);
                }
                if (isset($designation)) {
                    $allEmployees = $allEmployees->where('employees.designation', $designation);
                }
                if (isset($role)) {
                  $allEmployees = $allEmployees->where('employees.role', $role);
                }
                $totalFiltered = (clone $allEmployees)->get()->count();
                $employees =   $allEmployees
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get(['employees.*', 'employeegroups.name as employeegroups', 'designations.name as designations']);
            }
        } else {
            if (empty($request->input('search.value'))) {
                $allEmployees = Auth::user()->handleQuery('employee')->leftJoin('designations', 'employees.designation', 'designations.id');
                if (isset($employeegroup)) {
                    $allEmployees = $allEmployees->where('employees.employeegroup', $employeegroup);
                }
                if (isset($designation)) {
                    $allEmployees = $allEmployees->where('employees.designation', $designation);
                }
                if (isset($role)) {
                  $allEmployees = $allEmployees->where('employees.role', $role);
                }
            
                $totalFiltered = $allEmployees->count();
                $employees = $allEmployees
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get(['employees.*', 'designations.name as designations']);
            } elseif (!(empty($request->input('search.value')))) {
                $search = $request->input('search.value');

                $allEmployees = Auth::user()->handleQuery('employee')
                            ->leftJoin('employeegroups', 'employees.employeegroup', 'employeegroups.id')
                            ->leftJoin('designations', 'employees.designation', 'designations.id')
                            ->where(function ($query) use ($search) {
                                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.phone', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.email', 'LIKE', "%{$search}%");
                                $query->orWhere('employees.status', 'LIKE', "%{$search}%");
                                $query->orWhere('employeegroups.name', 'LIKE', "%{$search}%");
                                $query->orWhere('designations.name', 'LIKE', "%{$search}%");
                            });
            
                if (isset($employeegroup)) {
                    $allEmployees = $allEmployees->where('employees.employeegroup', $employeegroup);
                }
                if (isset($designation)) {
                    $allEmployees = $allEmployees->where('employees.designation', $designation);
                }
                if (isset($role)) {
                  $allEmployees = $allEmployees->where('employees.role', $role);
                }
                $totalFiltered = $allEmployees->count();
                $employees =   $allEmployees
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get(['employees.*', 'employeegroups.name as employeegroup_name', 'designations.name as designations']);
            }
        }

        $data = array();
        if (!empty($employees)) {
            $i = $start;
            // $logActivity = LogActivity::get();
            $manager = DB::table('managers')->where('company_id', $this->company_id)->first();
            $isManager = Manager::where('user_id', Auth::user()->id)->first();
            foreach ($employees as $employee) {
                $show =  domain_route('company.admin.employee.show', [$employee->id]);
                $edit =  domain_route('company.admin.employee.edit', [$employee->id]);

                $nestedData['id'] = ++$i;
                if (!empty($employee->image_path)) {
                    $path = URL::asset('cms/'.$employee->image_path);
                } else {
                    if ($employee->gender=='Male') {
                        $path = URL::asset('cms/storage/app/public/uploads/default_m.png');
                    } else {
                        $path = URL::asset('cms/storage/app/public/uploads/default_f.png');
                    }
                }

                $image_path = "<img src='$path' class='direct-chat-img' alt='User Image'>";

                $nestedData['name'] = "<div class='col-sm-3'>$image_path</div>"."<div class='col-sm-9'><p style='margin-top:5px;margin-left:10px;'><a href='{$show}'>{$employee->name}</a></p></div>";
                if (!empty($employee->phone)) {
                    if ($employee->country_code) {
                        $nestedData['phone'] = '+'.$employee->country_code.'-'.$employee->phone;
                    } else {
                        $nestedData['phone'] = $employee->phone;
                    }
                } else {
                    $nestedData['phone'] = 'N/A';
                }

                if (!empty($employee->email)) {
                    $nestedData['email'] = $employee->email;
                } else {
                    $nestedData['email'] = 'N/A';
                }

                // if ($employee->employeegroup!='') {
                    $nestedData['employeegroup'] = $employee->employee_groups?$employee->employee_groups->name:NULL;
                // } else {
                //     $nestedData['employeegroup'] = null;
                // }

                if ($employee->designation!='') {
                    $nestedData['designations'] = $employee->designations;
                } else {
                    $nestedData['designations'] = null;
                }

                $stat = null;
                if ($employee->status =='Active') {
                    $stat = "<span class='label label-success'>{$employee->status}</span>";
                } elseif ($employee->status =='Inactive') {
                    $stat = "<span class='label label-warning'>{$employee->status}</span>";
                } else {
                    $stat = "<span class='label label-danger'>{$employee->status}</span>";
                }

                if (Auth::user()->can('employee-status')) {
                    if ($employee->id==Auth::user()->EmployeeId()) {
                        $className = "alert-modal";
                    } else {
                        if ($employee->is_admin==1) {
                            $className = "alert-modal";
                        } else {
                            $className = "edit-modal";
                        }
                    }
                } else {
                    $className = "alert-modal";
                }
                $nestedData['status'] = "<a href='#' class='{$className}' data-id='{$employee->id}'
                        data-status='{$employee->status}'>{$stat}</a>";
                        
                if ($order=='last_action') {
                    if (isset($employee->last_action)) {
                        $formattedDate = Carbon::parse($employee->last_action);
                        $nestedData['last_action'] = $formattedDate->diffForHumans();
                    } else {
                        $nestedData['last_action'] = null;
                    }
                } else {
                    $last_action =  LogActivity::where('causer_id', $employee->user_id)->latest('created_at')->first();
                    if ($last_action) {
                        $nestedData['last_action'] = $last_action->created_at->diffForHumans();
                    } else {
                        $nestedData['last_action'] = null;
                    }
                }
                $delete = domain_route('company.admin.employee.destroy', [$employee->id]);
                if (getEmpActivity($employee->id)) {
                    if (Auth::user()->id == $manager->user_id) {
                        if ($employee->user_id == $manager->user_id) {
                            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                            if (Auth::user()->can('employee-update') && Auth::user()->EmployeeID()!=$employee->id) {
                                $nestedData['action']=$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                            }
                        } else {
                            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                            if (Auth::user()->can('employee-update')  && Auth::user()->EmployeeID()!=$employee->id) {
                                $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                            }
                            if (Auth::user()->can('employee-delete')) {
                                $nestedData['action'] =$nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='$employee->id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                            }
                        }
                    } elseif (Auth::user()->id != $manager->user_id && $employee->is_admin==1) {
                        $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                        if (Auth::user()->can('employee-update')  && Auth::user()->EmployeeID()!=$employee->id) {
                            $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                        }
                    } else {
                        $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                        if (Auth::user()->can('employee-update')  && Auth::user()->EmployeeID()!=$employee->id) {
                            $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                        }
                        if (Auth::user()->can('employee-delete')) {
                            $nestedData['action'] =$nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='$employee->id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                        }
                    }
                } else {
                    $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                    if (Auth::user()->can('employee-update')  && Auth::user()->EmployeeID()!=$employee->id && $employee->is_owner==0) {
                        $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                    } elseif ($employee->is_owner==1 && $isManager) {
                        $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                    }
                }
                $nestedData['accessibility'] =  $employee->role()->first()? $employee->role()->first()->name:null;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
          "draw"            => intval($request->input('draw')),
          "recordsTotal"    => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data"            => $data
          );

        echo json_encode($json_data);
    }

    public function customPdfExport(Request $request)
    {
        $getExportData = json_decode($request->exportedData)->data;
        $pageTitle = $request->pageTitle;
        set_time_limit(300);
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        $pdf = PDF::loadView('company.employees.exportpdf', compact('getExportData', 'pageTitle',  'properties', 'columns'))->setPaper('a4', 'portrait');
        $download = $pdf->download($pageTitle.'.pdf');
        return $download;
    }

    public function pdfexports(Request $request)
    {
        $getExportData = json_decode($request->exportedData);
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        $pageTitle = $request->pageTitle;
        $moduleName = $request->moduleName;
        $paperOrientation = "portrait";
        set_time_limit(300);
        ini_set("memory_limit", "256M");
        $pdf = PDF::loadView('company.employees.partial_show.exportpdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', $paperOrientation);
        $download = $pdf->download($pageTitle.'.pdf');
        return $download;
    }

    public function empOrderTable(Request $request)
    {
        if (config('settings.order_with_amt')==0) {
            $columns = array(
            0 =>'id',
            1 =>'order_no',
            2=> 'order_date',
            3=> 'partyname',
            4=> 'grand_total',
            5=> 'delivery_status',
            6=> 'action',
        );
        } else {
            $columns = array(
            0 =>'id',
            1 =>'order_no',
            2=> 'order_date',
            3=> 'partyname',
            4=> 'delivery_status',
            5=> 'action',
        );
        }
        $company_id = config('settings.company_id');
        $totalData =  Order::where('company_id', $company_id)->where('employee_id', $request->empID)->get()->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
        }
        $orders = Order::select('orders.*', 'employees.id as empID', 'employees.name as employee_name', 'clients.id as clientID', 'clients.company_name as partyname', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as status_name', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')
            ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->where('orders.company_id', $company_id)->where('orders.employee_id', $request->empID);
        if ($request->input('search.value')) {
            $orders = $orders->where(function ($query) use ($search) {
                $query->where('orders.id', 'LIKE', "%{$search}%");
                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
                $query->orWhere('orders.delivery_status', 'LIKE', "%{$search}%");
                $query->orWhere('orders.grand_total', 'LIKE', "%{$search}%");
                $query->orWhere('orders.order_date', 'LIKE', "%{$search}%");
                $query->orWhere('orders.order_no', 'LIKE', "%{$search}%");
                $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
            });
        }

        $total = $orders->sum('grand_total');
        $totalFiltered = $orders->get()->count();
        if ($order=='order_date') {
            $orders = $orders->orderBy('id', $dir);
        }
        $orders = $orders->orderBy($order, $dir)->offset($start)
                        ->limit($limit)
                        ->get();
        $data = array();
        if (!empty($orders)) {
            $i = $start;
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            foreach ($orders as $order) {
                $show =  domain_route('company.admin.order.show', [$order->id]);
                $edit =  domain_route('company.admin.order.edit', [$order->id]);
                $delete = domain_route('company.admin.order.destroy', [$order->id]);
                $clientShow = in_array($order->client_id, $viewable_clients)?domain_route('company.admin.client.show', [$order->client_id]):null;

                $nestedData['id'] = ++$i;
                $nestedData['orderno'] = "<a href='{$show}'>".getClientSetting()->order_prefix.$order->order_no."</a>";
                $nestedData['orderdate'] = getDeltaDate($order->order_date);
                $nestedData['partyname'] = "<a class='clientLinks' href='{$clientShow}' data-viewable='{$clientShow}'>{$order->partyname}</a>";

                if (config('settings.order_with_amt')==0) {
                    $nestedData['grandtotal'] = config('settings.currency_symbol').' '.number_format((float)$order->grand_total, 2);
                }

                $delivery_date = Carbon::parse($order->delivery_date)->format('Y-m-d');
                $spanTag = null;
                $orderStatus = $order->status_name;
                $statusColor = $order->color;
                $spanTag = "<span class='label' style='background: {$statusColor};'>{$orderStatus}</span>";
                if (Auth::user()->can('order-status')) {
                    $nestedData['delivery_status'] = "<a href='#' class='edit-modal-order' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                } else {
                    $nestedData['delivery_status'] = "<a href='#' class='alert-modal' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                }

                $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm'
                  style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

                if ($order->order_edit_flag == 1 && Auth::user()->can('order-update')) {
                    $nestedData['action'] = $nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm'
                  style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                }
                if ($order->order_delete_flag == 1 && Auth::user()->can('order-delete')) {
                    $nestedData['action'] = $nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='{ $order->id }' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                }
                
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
            "total"           => config('settings.currency_symbol').' '.number_format((float)$total, 2),
            );

        echo json_encode($json_data);
    }

    public function empCollectionTable(Request $request)
    {
        $columns = array(
          0 => 'id',
          1 => 'payment_date',
          2 => 'company_name',
          3 => 'payment_received',
          4 => 'payment_method',
          5 => 'action',
        );
        $company_id = config('settings.company_id');
        $totalData =  Collection::where('company_id', $company_id)->where('employee_id', $request->empID)->get()->count();
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
        }

        $collections = Collection::select('collections.*', 'clients.id as clientID', 'clients.company_name as partyname')
                ->leftJoin('clients', 'collections.client_id', 'clients.id')
                ->where('collections.employee_id', $request->empID)->where('collections.company_id', $company_id);
        if ($request->input('search.value')) {
            $collections=$collections->where(function ($query) use ($search) {
                $query->where('collections.id', 'LIKE', "%{$search}%");
                $query->orWhere('collections.payment_received', 'LIKE', "%{$search}%");
                $query->orWhere('collections.payment_method', 'LIKE', "%{$search}%");
                $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
            });
        }

        $total = $collections->sum('payment_received');
        $totalFiltered = $collections->get()->count();
        if ($order=='payment_date') {
            $collections = $collections->orderBy('id', $dir);
        }
        $collections = $collections->orderBy($order, $dir)->offset($start)
                        ->limit($limit)
                        ->get();
        $data = array();
        if (!empty($collections)) {
            $i = $start;
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            foreach ($collections as $collection) {
                $id = $collection->id;
                $received_payment = isset($collection->payment_received)?config('settings.currency_symbol').'   '.number_format((float)$collection->payment_received, 2):null;
                $payment_date = isset($collection->payment_date)?getDeltaDate(date('Y-m-d', strtotime($collection->payment_date))):null;
                $status = $collection->status;
                $client_show = in_array($collection->client_id, $viewable_clients)?domain_route('company.admin.client.show', [$collection->client_id]):null;
                $employee_show = domain_route('company.admin.employee.show', [$collection->employee_id]);
                $show = domain_route('company.admin.collection.show', [$id]);
                $edit = domain_route('company.admin.collection.edit', [$id]);
                $delete = domain_route('company.admin.collection.destroy', [$id]);

                $nestedData['id'] = ++$i;
                $nestedData['payment_date'] = $payment_date;
                $nestedData['company_name'] = "<a dataparty='".$collection->partyname."' class='clientLinks' href='{$client_show}' data-viewable='{$client_show}'>".$collection->partyname."</a>" ;
                $nestedData['payment_received'] = $received_payment;
                $nestedData['payment_method'] = $collection->payment_method;
                $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                if (Auth::user()->can('collection-update')) {
                    $nestedData['action'] =$nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                }
                if (Auth::user()->can('collection-delete')) {
                    $nestedData['action'] =$nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                }
                $data[] = $nestedData;
            }
        }

        $json_data = array(
          "draw"            => intval($request->input('draw')),
          "recordsTotal"    => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data"            => $data,
          "total"           => config('settings.currency_symbol').' '.number_format((float)$total, 2),
      );

        return json_encode($json_data);
    }

    public function empDayremarksTable(Request $request)
    {
        $columns = array(
          0 => 'id',
          1 => 'remark_date',
          2 => 'remark_datetime',
          3 => 'remarks',
          4 => 'action',
        );

        $company_id = config('settings.company_id');
        $empVal = [$request->empID];
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $prepQuery = Auth::user()->handleQuery('day_remark')
                ->leftJoin('employees', 'employees.id', 'day_remarks.employee_id')
                ->select('day_remarks.id', 'day_remarks.employee_id', 'day_remarks.remark_date', 'day_remarks.remark_datetime', 'day_remarks.remarks', 'employees.name as employee_name');
        if (!empty($empVal)) {
            $empFilterQuery =  $prepQuery;
            $prepQuery = $empFilterQuery->whereIn('day_remarks.employee_id', $empVal);
        }
     
        if (!empty($search)) {
            $searchQuery = $prepQuery;
            $prepQuery = $searchQuery->where(function ($query) use ($search) {
                $query->orWhere('day_remarks.remarks', 'LIKE', "%{$search}%");
                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
            });
        }

        $totalData =  $prepQuery->count();
        $totalFiltered = $totalData;
      
        $data = array();
        if ($order=='remark_date' ||$order=='remark_datetime') {
            $prepQuery = $prepQuery->orderBy('day_remarks.id', $dir);
        }
        $dayremarks = $prepQuery->orderBy($order, $dir)->offset($start)
                            ->limit($limit)
                            ->get();
        if (!empty($dayremarks)) {
            $i = $start;
            foreach ($dayremarks as $dayremark) {
                $id = $dayremark->id;
                $employee_name = $dayremark->employee_name;
                $dayremark_date = $dayremark->remark_date;
                $dayremark_time = date("H:i A", strtotime($dayremark->remark_datetime));
                $employee_show = domain_route('company.admin.employee.show', [$dayremark->employee_id]);
                $show = domain_route('company.admin.remark.show', [$id]);

                $nestedData['id'] = ++$i;
                $nestedData['remark_date'] = getDeltaDate($dayremark_date);
                $nestedData['remark_datetime'] = $dayremark_time;
                $nestedData['remarks'] = $dayremark->remarks;
            
                $nestedData['action'] = '';
                if (Auth::user()->can('dayremark-view')) {
                    $nestedData['action'] = $nestedData['action']."<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                }
                $data[] = $nestedData;
            }
        }

        $json_data = array(
        "draw"            => intval($request->input('draw')),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data,
      );

        return json_encode($json_data);
    }

    public function create()
    {
        $company_id = config('settings.company_id');
        $comp_plan = getCompanyPlan($company_id);
        $comp_plan->users = Company::find($company_id)->num_users;
        $employeescount = Auth::user()->handleQuery('employee')
        ->where('status', 'Active')
        ->orderBy('created_at', 'desc')
        ->get()->count();
        $superiors = [];
        if (Auth::user()->isCompanyEmployee()) {
            $empData = Employee::where('company_id', $company_id)->where('id', Auth::user()->employeeID())->first();
            $subDesignationExists = Designation::where('company_id', $company_id)->where('parent_id', $empData->designation)->first();
            if (!$subDesignationExists) {
                session()->flash('alert', 'You are not authorized to create employee');
                return redirect()->back();
            }
        }
        $banks = Bank::where('company_id', $company_id)->pluck('name', 'id');
        if ($employeescount >= $comp_plan->users) {
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        } else {
            $clients = Auth::user()->handleQuery('client')->select('company_name', 'id')->where('status', 'Active')->orderBy('company_name', 'asc')->get();
            $employeegroups = EmployeeGroup::where('company_id', $company_id)->where('status', '=', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
            if (Auth::user()->isCompanyEmployee()) {
                $empRow = Employee::select('employees.id as employeeID', 'employees.name as EmployeeName', 'employees.designation', 'employees.superior', 'designations.id as designationID', 'designations.name as designationName')->leftJoin('designations', 'employees.designation', 'designations.id')->where('employees.company_id', $company_id)->where('employees.id', Auth::user()->EmployeeId())->first();
                $subDesignation=[];
                $subDesignation=$this->getSubDesignations($empRow->designation, $subDesignation);
                $designations = Designation::where('company_id', $company_id)->whereIn('id', $subDesignation)->get();
            } else {
                $empRow = null;
                $designations = Designation::where('company_id', $company_id)->select('name', 'id')->get();
            }
            $setting = ClientSetting::select('phonecode')->where('company_id', $company_id)->first();
            $country_codes = DB::table('countries')->select('name', 'phonecode')->get();
            $beat_clients = Auth::user()->handleQuery('client')->select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid', 'partytypes.id as partytypeid', 'partytypes.short_name as shortName')
        ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
        ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
        ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
        ->where('clients.status', 'Active')
        ->orderBy('beats.name', 'desc')
        ->get();
            $beats = array();
            foreach ($beat_clients as $beat_client) {
                if ($beat_client->beatid!='') {
                    $beats[$beat_client->beatid]['name']=$beat_client->beat_name;
                    $beats[$beat_client->beatid]['id']=$beat_client->beatid;
                    if (isset($beat_client->shortName)) {
                        $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                    } else {
                        $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
                    }
                } else {
                    $beats[0]['name']='Unspecified';
                    $beats[0]['id']='0';
                    $beats[0]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                    if (isset($beat_client->shortName)) {
                        $beats[0]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                    } else {
                        $beats[0]['clients'][$beat_client->id]=$beat_client->company_name;
                    }
                }
            }
            $childExists = null;
            $roles = Role::select('id', 'name')->where('company_id', $company_id)->get();
            return view('company.employees.create', compact('employeegroups', 'country_codes', 'clients', 'beats', 'setting', 'designations', 'superiors', 'banks', 'empRow', 'childExists', 'roles'));
        }
    }


    public function store(Request $request)
    {
        $config = (object)config('settings');
        $company_id = $config->company_id;
        // $company_plan = DB::table('company_plan')->select('plan_id')->where('company_id', $company_id)->first();
        $getCompany = Company::find($company_id);
        $num_user_plan = $getCompany->num_users;//DB::table('plans')->select('users')->where('id', $company_plan->plan_id)->first();
        $num_user_company = Employee::where('company_id', $company_id)->where('status', 'Active')->whereNull('deleted_at')->count();
        if ($num_user_company >= $num_user_plan) {
            return back()->with('maxreached', 'You have reached maximum number of users limit. Please archive some users to add new users');
        }

        $customMessages = [
        'name.required' => 'This field is required.',
        'name.unique' => 'Name and Phone Combination already exists.',
        'phone.required' => 'This field is required.',
        'phone.digits' => 'Mobile No. should be numeric and exactly 10 digits.',
        'phone.unique' => 'Mobile No. already exists.',
        'employee_code.unique' => 'Employee Code Should be unique.',
        'email.unique' => 'Email already exists.',
        'e_phone.digits' => 'The emergency contact shoud be 10 digits.',
    ];
        $designationAdmin = Designation::where('company_id', $company_id)->where('id', $request->designation)->first();
        if (Auth::user()->isCompanyManager() && isset($designationAdmin) && $designationAdmin->name=="Admin") {
            $validator = Validator::make($request->all(), [
            'name' => 'required',
            'employee_code' => 'sometimes|nullable|unique:employees,employee_code,NULL,id,deleted_at,NULL,company_id,' . $company_id,
            'phone' => 'required|digits_between:7,14|unique:employees,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id,
            'password'=>' required|min:8',
            'c_password' => 'same:password',
            'a_phone' => 'sometimes|nullable|digits_between:7,14',
            'e_phone' => 'sometimes|nullable|digits_between:7,14',
            'designation'=>'required',
        ], $customMessages);
        } else {
            $validator = Validator::make($request->all(), [
            'name' => 'required',
            'employee_code' => 'sometimes|nullable|unique:employees,employee_code,NULL,id,deleted_at,NULL,company_id,' . $company_id,
            'phone' => 'required|digits_between:7,14|unique:employees,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id,
            'password'=>' required|min:8',
            'c_password' => 'same:password',
            'a_phone' => 'sometimes|nullable|digits_between:7,14',
            'e_phone' => 'sometimes|nullable|digits_between:7,14',
            'designation'=>'required',
            'superior' => 'required',
        ], $customMessages);
        }

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if ($request->get('email')) {
            $this->validate($request, [
            'email' => 'required|email|unique:employees,email,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,email,NULL,id,deleted_at,NULL,company_id,' . $company_id,
        ]);
        }

        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $employee = new \App\Employee;
        $employee->company_id = $company_id;
        $employee->name = $request->get('name');
        $employee->employeegroup = $request->get('employeegroup');
        $employee->employee_code = $request->get('employee_code');
        $employee->phone = $request->get('phone');
        $employee->email = $request->get('email');
        $employee->password = $request->get('password');
        if (isset($request->englishDate)) {
            $employee->b_date = $request->get('englishDate');
        } else {
            $employee->b_date = $request->get('b_date');
        }
        if (isset($request->englishDoj)) {
            $employee->doj = $request->get('englishDoj');
        } else {
            $employee->doj = $request->get('doj');
        }
        if (isset($request->englishLwd)) {
            $employee->lwd = $request->get('englishLwd');
        } else {
            $employee->lwd = $request->get('lwd');
        }
        if ($request->role) {
            $employee->role = $request->role;
        } else {
            $defaultRole = Role::where('company_id', $company_id)->where('name', 'Limited Access')->first();
            $employee->role = $defaultRole->id;
        }
        $employee->gender = $request->get('gender');
        $employee->local_add = $request->get('local_add');
        $employee->per_add = $request->get('per_add');
        if ($employee->status) {
            $employee->status = $request->get('status');
        } else {
            $employee->status = 'Active';
        }
        $employee->country_code = $request->get('country_code');
        $employee->alt_country_code = $request->get('alt_country_code');
        $employee->e_country_code = $request->get('e_country_code');
        $employee->father_name = $request->get('father_name');
        $employee->a_phone = $request->get('a_phone');
        $employee->designation = $request->get('designation');
        if ($request->superior) {
            $employee->superior = $request->get('superior');
        } else {
            $employee->is_admin = 1;
        }
        $employee->total_salary = $request->get('total_salary');
        $employee->permitted_leave = $request->get('permitted_leave');
        $employee->acc_holder = $request->get('acc_holder');
        $employee->acc_number = $request->get('acc_number');
        $employee->bank_id = $request->get('bank_id');
        $employee->ifsc_code = $request->get('ifsc_code');
        $employee->pan = $request->get('pan');
        $employee->branch = $request->get('branch');
        $employee->e_name = $request->get('e_name');
        $employee->e_relation = $request->get('e_relation');
        $employee->e_phone = $request->get('e_phone');

        if ($request->file('resume')) {
            $field = 'resume';
            $employee->resume = $this->storeDocs($request, $field);
        }

        if ($request->file('offer_letter')) {
            $field = 'offer_letter';
            $employee->offer_letter = $this->storeDocs($request, $field);
        }

        if ($request->file('joining_letter')) {
            $field = 'joining_letter';
            $employee->joining_letter = $this->storeDocs($request, $field);
        }

        if ($request->file('contract')) {
            $field = 'contract';
            $employee->contract = $this->storeDocs($request, $field);
        }

        if ($request->file('id_proof')) {
            $field = 'id_proof';
            $employee->id_proof = $this->storeDocs($request, $field);
        }

        if ($request->file('image')) {
            $this->validate($request, [
              'image' => 'mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:1000',
            ]);
            $image2 = $request->file('image');
            $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image2->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image2->storeAs('public/uploads/' . $companyName . '/employees/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/employees/' . $new_name);
            $employee->image = $new_name;
            $employee->image_path = $path;
        }
    
        $saved = $employee->save();
        if ($saved) {
          sendPushNotification_(getFBIDs($company_id), 40, null, array("data_type" => "employee", "employee_id"=>$employee->id,"employee" => $employee, "action" => "add"));

            if ($request->get('status')=='Active') {
                $active=2;
            } else {
                $active=1;
            }

            $user = new User;
            
            $user->email = $request->get('email');
            $user->name = $request->get('name');
            $user->phone = $request->get('phone');
            $user->company_id= $company_id;
            $user->is_active = $active;
            $user->password = bcrypt($request->get('password'));
            $user->save();
            if ($request->role) {
                $user->assignRole($request->role);
            } else {
                $user->assignRole($employee->role);
            }
            $employee->assignUser($user);
            $employee->save();

        }

        if ($saved && !empty($request->clients)) {
            $employee_id = $employee->id;
            $map_type = 1;
            if (getClientSetting()->beat==1) {
                $emp_beats = (explode(',', $request->beatIds));
                foreach ($emp_beats as $emp_beat) {
                    $handle_beat = DB::table('employee_handle_beats')->insert([
                    'employee_id' => $employee_id,
                    'beat_id' => $emp_beat
                ]);
                }
            }
        
            if (implode(",", $request->clients) == 0) {
                $clients = Client::where('company_id', $company_id)->pluck('id');
            } else {
                $clients = $request->clients;
            }

            foreach ($clients as $client_id) {
                $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    DB::table('handles')->insert([
                    'company_id' => $company_id,
                    'employee_id' => $employee_id,
                    'client_id' => $client_id,
                    'map_type' => $map_type
                ]);
                }

                unset($handle);
            }
        }

        if ((Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()) && $employee->is_admin==1) {
            $clients = Client::where('company_id', $company_id)->pluck('id');
            foreach ($clients as $client_id) {
                $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    DB::table('handles')->insert([
                        'company_id' => $company_id,
                        'employee_id' => $employee->id,
                        'client_id' => $client_id,
                        'map_type' => 1
                    ]);
                }

                unset($handle);
            }
        }

        if($employee->superior){
          $immediateJuniors = Employee::where('superior', $employee->superior)->pluck('id')->toArray();
          $isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;

          sendPushNotification_(getFBIDs($company_id, null, $employee->superior), 40, null, array("data_type" => "employee", "employee_id"=>$employee->superior,"isLowestInChain" => $isLowestInChain, "action" => "update_superior"));

        }
              

        return redirect()->route('company.admin.employee', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    public function ajaxValidate(Request $request)
    {
        $company_id = config('settings.company_id');
        $customMessages = [
          'name.required' => 'This field is required.',
          'name.unique' => 'Name and Phone Combination already exists.',
          'phone.required' => 'This field is required.',
          'phone.digits' => 'Mobile No. should be numeric and exactly 10 digits.',
          'phone.unique' => 'Mobile No. already exists.',
          'employee_code.unique' => 'Employee Code Should be unique.',
          'email.unique' => 'Email already exists.',
          'e_phone.digits' => 'The emergency contact shoud be 10 digits.',
      ];
        $designationAdmin = Designation::where('company_id', $company_id)->where('id', $request->designation)->first();
        if (Auth::user()->isCompanyManager() && isset($designationAdmin) && $designationAdmin->name=="Admin") {
            $validator = Validator::make($request->all(), [
              'name' => 'required',
              'employee_code' => 'sometimes|nullable|unique:employees,employee_code,NULL,id,deleted_at,NULL,company_id,' . $company_id,
              'phone' => 'required|digits_between:7,14|unique:employees,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id,
              'password'=>' required|min:8',
              'c_password' => 'same:password',
              'a_phone' => 'sometimes|nullable|digits_between:7,14',
              'e_phone' => 'sometimes|nullable|digits_between:7,14',
              'designation'=>'required|integer',
          ], $customMessages);
        } else {
            $validator = Validator::make($request->all(), [
              'name' => 'required',
              'employee_code' => 'sometimes|nullable|unique:employees,employee_code,NULL,id,deleted_at,NULL,company_id,' . $company_id,
              'phone' => 'required|digits_between:7,14|unique:employees,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,NULL,id,deleted_at,NULL,company_id,' . $company_id,
              'password'=>' required|min:8',
              'c_password' => 'same:password',
              'a_phone' => 'sometimes|nullable|digits_between:7,14',
              'e_phone' => 'sometimes|nullable|digits_between:7,14',
              'designation'=>'required|integer',
              'superior' => 'required',
              'email' => 'sometimes|email|nullable|unique:employees,email,NULL,id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,email,NULL,id,deleted_at,NULL,company_id,' . $company_id,
          ], $customMessages);
        }
        if ($validator->fails()) {
            return response()->json(['result'=>false,'code'=>201,'error'=>$validator->getMessageBag()->toArray()]);
        }
        $data['result'] = true;
        return $data;
    }

    public function ajaxUpdateValidate(Request $request)
    {
        $company_id = config('settings.company_id');
        $employee = Employee::where('company_id', $company_id)->where('id', $request->id)->first();
        $user = User::where('company_id', $company_id)->where('id', $employee->user_id)->first();
        $customMessages = [
          'name.required' => 'This field is required.',
          'name.unique' => 'Name and Phone Combination already exists.',
          'phone.required' => 'This field is required.',
          'phone.digits' => 'Mobile No. should be numeric and exactly 10 digits.',
          'phone.unique' => 'Mobile No. already exists.',
          'employee_code.unique' => 'Employee Code Should be unique.',
          'email.unique' => 'Email already exists.',
          'e_phone.digits' => 'The emergency contact shoud be 10 digits.',
      ];
        $designationAdmin = Designation::where('company_id', $company_id)->where('id', $request->designation)->first();
        if (Auth::user()->isCompanyManager() && isset($designationAdmin) && $designationAdmin->name=="Admin") {
            $validator = Validator::make($request->all(), [
              'name' => 'required',
              'employee_code' => 'nullable|unique:employees,employee_code,' . $request->id . ',id,deleted_at,NULL,company_id,' . $company_id,
              'phone' => 'required|digits_between:7,14|unique:employees,phone,' . $request->id . ',id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,' . $user->id . ',id,deleted_at,NULL,company_id,' . $company_id,
              'a_phone' => 'sometimes|nullable|digits_between:7,14',
              'e_phone' => 'sometimes|nullable|digits_between:7,14',
              'designation' => 'required|integer',
          ], $customMessages);
        } else {
            $validator = Validator::make($request->all(), [
              'name' => 'required',
              'employee_code' => 'nullable|unique:employees,employee_code,' . $request->id . ',id,deleted_at,NULL,company_id,' . $company_id,
              'phone' => 'required|digits_between:7,14|unique:employees,phone,' . $request->id . ',id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,' . $user->id . ',id,deleted_at,NULL,company_id,' . $company_id,
              'a_phone' => 'sometimes|nullable|digits_between:7,14',
              'e_phone' => 'sometimes|nullable|digits_between:7,14',
              'designation' => 'required|integer',
              'email' => 'sometimes|email|nullable|unique:employees,email,' . $request->id . ',id,deleted_at,NULL,company_id,' . $company_id . '|unique:users,email,' . $user->id . ',id,deleted_at,NULL,company_id,' . $company_id,
          ], $customMessages);
        }

        if ($validator->fails()) {
            return response()->json(['result'=>false,'code'=>201,'error'=>$validator->getMessageBag()->toArray()]);
        }
        if ($request->get('password')) {
            $vpassword = Validator::make($request->all(), [
          'password'=>' required|min:8',
          'c_password' => 'same:password',
        ]);
            if ($vpassword->fails()) {
                return response()->json(['result'=>false,'code'=>201,'error'=>$vpassword->getMessageBag()->toArray()]);
            }
        }
        $data['result'] = true;
        return $data;
    }

    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $time = time();
        $employee = Auth::user()->handleQuery('employee')->select('employees.*', 'designations.id as designation_id', 'designations.name as designation_name', 'banks.id as bank_id', 'banks.name as bank_name')
                  ->where('employees.id', $request->id)
                  ->leftJoin('designations', 'employees.designation', 'designations.id')
                  ->leftJoin('banks', 'employees.bank_id', 'banks.id')->first();
        $is_logged_in = (bool)$employee->user->is_logged_in;
        if (!$employee) {
            session()->flash('alert', 'Sorry! You are not authorized to view this user details.');
            return redirect()->back();
        }
        if ($employee->superior) {
            $superior_row = Employee::where('id', $employee->superior)->first();
          
            $superior_name = $superior_row->name;
        } else {
            $superior_name = null;
        }
        // $user = User::where('id',$employee->user_id)->first();
        $isManager = Manager::where('user_id', Auth::user()->id)->first();
        if ($isManager) {
            $isManager = 'true';
        } else {
            $isManager = 'false';
        }
        $employeegroups = EmployeeGroup::where('company_id', $company_id)->get();
        $holidays = Holiday::where('company_id', $company_id)->get();
        $today_date = Carbon::now()->addDays(1)->format('Y-m-d');
        $current_date = Carbon::now()->format('Y-m-d');
        $holiday_list = [];
        $beforeTodays = [];
        $data = [];
        $superiors =[];
        $banks = Bank::where('company_id', $company_id)->pluck('name', 'id');
        $childExists = Auth::user()->handleQuery('employee')->where('superior', $employee->id)->first();
        if ($childExists) {
            $childExists=true;
        } else {
            $childExists=false;
        }
        $empId = Auth::user()->EmployeeId();
        if (Auth::user()->isCompanyManager()) {
            $allSup = [];
        } else {
            $allSup = [];
            $allSup = Auth::user()->getUpChainUsers($empId, $allSup);
            array_pop($allSup);
            $empAdmins = Employee::where('company_id', $company_id)->select('id')->where('is_admin', 1)->get();
            foreach ($empAdmins as $emp) {
                array_push($allSup, $emp->id);
            }
        }
        $activities = Activity::where('company_id', $company_id)->where(function ($q) use ($request) {
            $q= $q->where('created_by', $request->id)->orWhere('assigned_to', $request->id);
        })->orderBy('id', 'DESC')->orderBy('start_datetime', 'DESC')->get();

        $attendances = Attendance::where('company_id', $company_id)->groupBy('adate')->where('employee_id', $request->id)->orderBy('adate', 'ASC')->get();
        $designations = Designation::where('company_id', $company_id)->pluck('name', 'id');
        $country_codes = DB::table('countries')->select('name', 'phonecode')->get();
        $parties = Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'ASC')->get();
        $links = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->id)->pluck('client_id')->toArray();
        $partyhandles = $suppartyhandles = DB::table('handles')->where('company_id', $company_id)->get();
        $beats = array();
        $relatedPaties=$employee->parties;
        if ($employee->superior) {
            $supHandles = $suppartyhandles->where('employee_id', $employee->superior)->pluck('client_id')->toArray();
            $relatedPaties = $relatedPaties->whereIn('id', $supHandles);
            $beat_clients = Client::select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid', 'partytypes.id as partytypeid', 'partytypes.short_name as shortName')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereIn('clients.id', $supHandles)
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('beats.name', 'desc')
              ->get();
        } else {
            $beat_clients = Client::select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid', 'partytypes.id as partytypeid', 'partytypes.short_name as shortName')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('beats.name', 'desc')
              ->get();
        }
        foreach ($beat_clients as $beat_client) {
            if ($beat_client->beatid!='') {
                $beats[$beat_client->beatid]['name']=$beat_client->beat_name;
                $beats[$beat_client->beatid]['id']=$beat_client->beatid;
                if (isset($beat_client->shortName)) {
                    $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                } else {
                    $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
                }
            } else {
                $beats[$time]['name']='Unspecified';
                $beats[$time]['id']='0';
                if (isset($beat_client->shortName)) {
                    $beats[$time]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                } else {
                    $beats[$time]['clients'][$beat_client->id]=$beat_client->company_name;
                }
            }
        }

        ksort($beats);
        $handles = DB::table('handles')->select('client_id', 'employee_id')->where('employee_id', $request->id)->pluck('client_id')->toArray();
        $getEmployeeJuniors = $this->getEmployeeAllJunior($employee->id, $juniors=[]);
        $getJuniorParties = DB::table('handles')->select('client_id')->whereIn('employee_id', $getEmployeeJuniors)->pluck('client_id')->toJson();

        $employeeFirstAttenDay = Employee::where('company_id', $company_id)->where('id', $request->id)->first();
        $empCDate= Carbon::parse($employeeFirstAttenDay->created_at)->format('Y-m-d');
        if (isset($empCDate)) {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $empCDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        } else {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $request->engFirstDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        }
        foreach ($holidaysDatesLatestDays as $holiday) {
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            while ($start_date<=$end_date) {
                $holiday_list[] = $start_date->format('Y-m-d');
                $start_date = $start_date->addDays(1);
            }
        }

        if (isset($empCDate)) {
            $start_date = Carbon::parse($empCDate);
        } else {
            $start_date = Carbon::parse($today_date);
        }

        $end_date = Carbon::now();
        while ($start_date <= $end_date) {
            $beforeTodays[] = $start_date->format('Y-m-d');
            $start_date = $start_date->addDays(1);
        }
        foreach ($holidays as $holiday) {
            $end_date = Carbon::parse($holiday->end_date);
            $data['nextday_end'][$holiday->id] = $end_date->addDays(1);
        }
        foreach ($holiday_list as $holiday) {
            if (($key = array_search($holiday, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach ($attendances as $attendance) {
            if (($key = array_search($attendance->adate, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        $chainTransfer =[];
        $chainTransfer = $this->getChainTransfer($employee->id);
        $orderStatus = \App\ModuleAttribute::where('company_id', $company_id)
              ->where('module_id', 1)->get();
        $ordersCount = Order::where('company_id', $company_id)->where('employee_id', $request->id)->get()->count();
        $collectionsCount = Collection::where('company_id', $company_id)->where('employee_id', $request->id)->get()->count();
        $juniors = Employee::leftJoin('designations', 'employees.designation', 'designations.id')->where('employees.company_id', $company_id)->where('employees.superior', $employee->id)->select('employees.id', 'employees.name as employee_name', 'designations.id as designationID', 'designations.name as designation_name')->where('status', 'Active')->get();
        $roles = Role::select('id', 'name')->where('company_id', $company_id)->get();
        if ($employee->role) {
            $role = Role::where('id', $employee->role)->first();
            $role_name = $role->name;
        } else {
            $role_name = null;
        }
        $dayRemarksCount = DayRemark::where('company_id', $company_id)->where('employee_id', $request->id)->get()->count();
        if ($employee) {
            return view('company.employees.show', compact('employee', 'is_logged_in', 'handles', 'holidays', 'beforeTodays', 'data', 'parties', 'beats', 'employeegroups', 'designations', 'superiors', 'banks', 'country_codes', 'superior_name', 'childExists', 'orderStatus', 'activities', 'allSup', 'ordersCount', 'collectionsCount', 'chainTransfer', 'juniors', 'getJuniorParties', 'relatedPaties', 'roles', 'role_name', 'dayRemarksCount', 'isManager'));
        } else {
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        }
    }

    public function transferUser(Request $request)
    {
        $data=[];
        $data['result'] = false;
        $company_id= config('settings.company_id');
        $fromHandles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->pluck('client_id')->toArray();
        $toHandles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->transfer_to)->pluck('client_id')->toArray();
        $clients_ids = array_diff($fromHandles, $toHandles);
        $juniorsUpdated=Employee::where('company_id', $company_id)->where('superior', $request->employee_id)->update(['superior'=>$request->transfer_to]);
        DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->whereIn('client_id', $clients_ids)->update(['employee_id'=>$request->transfer_to]);
        DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->delete();
        $fromAssessibility = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->pluck('client_id')->toArray();
        $toAccessibility = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->transfer_to)->pluck('client_id')->toArray();
        $assessible_ids = array_diff($fromAssessibility, $toAccessibility);
        DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->whereIn('client_id', $assessible_ids)->update(['employee_id'=>$request->transfer_to]);
        DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->employee_id)->delete();
        $data['transferred_from'] = $request->employee_id;
        $data['transfer_to'] = $request->transfer_to;
        $data['client_ids']= $fromHandles;
        $data['accessibility_ids']=$fromAssessibility;
        $data['action'] = "Transferred";
        $getFBIDs = Employee::where('company_id', $company_id)->pluck('firebase_token')->toArray();
        sendPushNotification_($getFBIDs, 26, 'transferred successfully', $data);
        $data['result'] = true;
        return $data;
    }

    private function getChainTransfer($id)
    {
        $data=[];
        $authSuperiorDesignations=[];
        $empSuperiorDesignations=[];
        $diffArray=[];
        $company_id = config('settings.company_id');
        $employee = Employee::where('id', $id)->where('company_id', $company_id)->first();
        $empSuperior = Employee::where('id', $employee->superior)->where('company_id', $company_id)->first();
        $transferableEmployees = Employee::where('company_id', $company_id)->where('designation', $employee->designation)->where('superior', $employee->superior)->where('id', '!=', $id)->pluck('id')->toArray();
        if ($empSuperior) {
            $supDesignation = Designation::where('company_id', $company_id)->where('id', $empSuperior->designation)->first();
            $data[$supDesignation->name][$employee->superior]['id']=$employee->superior;
            $data[$supDesignation->name][$employee->superior]['emp_name']=$empSuperior->name;
        }

        $designation = Designation::where('company_id', $company_id)->where('id', $employee->designation)->first();
        if ($designation) {
            $employees = Employee::select('id', 'name')->where('company_id', $company_id)->whereIn('id', $transferableEmployees)->where('status', 'Active')->get();
            foreach ($employees as $employee) {
                $data[$designation->name][$employee->id]['id']= $employee->id;
                $data[$designation->name][$employee->id]['emp_name']=$employee->name;
            }
        }
        return $data;
    }

    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $time = time();
        if (Auth::user()->isCompanyEmployee()) {
            if (Auth::user()->EmployeeId() == $request->id) {
                return redirect()->route('company.admin.employee', ['domain' => domain()]);
            }
        }
        $employee = Auth::user()->handleQuery('employee')->where('id', $request->id)->first();
        if (!$employee) {
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        }
        $isManager = Manager::where('user_id', Auth::user()->id)->first();
        if (!$isManager && $employee->is_owner==1) {
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        }
        $empSuperior = Employee::where('company_id', $company_id)->where('superior', $employee->superior)->first();
        $employeegroups = EmployeeGroup::where('company_id', $company_id)->where('status', '=', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $country_codes = DB::table('countries')->select('name', 'phonecode')->get();
        $clients = Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        $superiors = [];
        $childExists = Auth::user()->handleQuery('employee')->where('superior', $employee->id)->first();
        $handleExists = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->first();
        if ($childExists || $handleExists) {
            $childExists=true;
        } else {
            $childExists=null;
        }
        $user = User::where('id', $employee->user_id)->first();
        $isManager = Manager::where('user_id', $user->id)->first();
        if ($isManager) {
            $isManager = true;
        }
        $empRow = Employee::where('company_id', $company_id)->where('id', Auth::user()->EmployeeId())->first();
        $empDesignation=Designation::where('id', $empRow->designation)->first();
        $empDesignationName= $empDesignation->name;
        $subDesignation=[];
        $subDesignation=$this->getSubDesignations($empRow->designation, $subDesignation);
        if (Auth::user()->isCompanyManager()) {
            array_unshift($subDesignation, $empRow->designation);
        }
        $empsubDesignation = [];
        $empsubDesignation = $this->getSubDesignations($employee->designation, $empsubDesignation);
        $subDesignation = array_diff($subDesignation, $empsubDesignation);
        $designations = Designation::where('company_id', $company_id)->whereIn('id', $subDesignation)->get();
        $links = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->id)->pluck('client_id')->toArray();
        $banks = Bank::where('company_id', $company_id)->pluck('name', 'id');
        $beats = array();
        $partyhandles = $suppartyhandles = DB::table('handles')->where('company_id', $company_id)->get();
        $handles = $partyhandles->where('employee_id', $request->id)->pluck('client_id')->toArray();
        if ($employee->superior) {
            $supHandles = $suppartyhandles->where('employee_id', $employee->superior)->pluck('client_id')->toArray();
            $beat_clients = Client::select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid', 'partytypes.id as partytypeid', 'partytypes.short_name as shortName')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereIn('clients.id', $supHandles)
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('beats.id', 'asc')
              ->get();
        } else {
            $beat_clients = Client::select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid', 'partytypes.id as partytypeid', 'partytypes.short_name as shortName')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('beats.name', 'asc')
              ->get();
        }
        foreach ($beat_clients as $beat_client) {
            if ($beat_client->beatid!='') {
                $beats[$beat_client->beatid]['name']=$beat_client->beat_name;
                $beats[$beat_client->beatid]['id']=$beat_client->beatid;
                if (isset($beat_client->shortName)) {
                    $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                } else {
                    $beats[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
                }
            } else {
                $beats[$time]['name']='Unspecified';
                $beats[$time]['id']='0';
                if (isset($beat_client->shortName)) {
                    $beats[$time]['clients'][$beat_client->id]=$beat_client->company_name.' ('.$beat_client->shortName.')';
                } else {
                    $beats[$time]['clients'][$beat_client->id]=$beat_client->company_name;
                }
            }
        }
        ksort($beats);


        $getEmployeeJuniors = $this->getEmployeeAllJunior($request->id, $juniors=[]);
        $getJuniorParties = DB::table('handles')->select('client_id')->whereIn('employee_id', $getEmployeeJuniors)->pluck('client_id')->toJson();

        $handles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->id)->pluck('client_id')->toArray();
        $roles = Role::select('id', 'name')->where('company_id', $company_id)->get();
        return view('company.employees.edit', compact('employee', 'employeegroups', 'country_codes', 'clients', 'beats', 'handles', 'banks', 'designations', 'superiors', 'childExists', 'empSuperior', 'empRow', 'empDesignationName', 'getJuniorParties', 'roles', 'isManager'));
    }


    public function update(Request $request, $domain, $id)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $employee = Employee::where('company_id', $company_id)->where('id', $id)->first();
        Session::flash('DT_EMP_filters', $request->DT_EMP_FILTER);
        if (!$employee) {
            Session::flash('alert', 'Requested Employee not found');
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        }

        // checking number of active users exceeded
        if ($employee->status == 'Inactive' && $request->status == 'Active') {
            $comp_plan = getCompanyPlan($company_id);
            $comp_plan->users;
            $employeescount = Employee::where('company_id', $company_id)->where('status', 'Active')->count();
            if ($employeescount >= $comp_plan->users) {
                Session::flash('alert', 'Employee could not be activated as maximum number has been reached');
                return redirect()->route('company.admin.employee', ['domain' => domain()]);
            }
        }

        $user = User::where('company_id', $company_id)->where('id', $employee->user_id)->first();
        $customMessages = [

          'name.required' => 'This field is required.',
          'name.unique' => 'Name and Phone Combination already exists.',
          'phone.required' => 'This field is required.',
          'phone.digits' => 'Mobile No. should be numeric and exactly 10 digits.',
          'phone.unique' => 'Mobile No. already exists.',
          'employee_code.unique' => 'Employee Code Should be unique.',
          'email.unique' => 'Email already exists.',
      ];
        if ($user) {
            $this->validate($request, [
              'phone' => 'required|digits_between:7,14|unique:employees,phone,' . $id . ',id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,phone,' . $user->id . ',id,deleted_at,NULL,company_id,' . $company_id,
          ]);
        } else {
            $this->validate($request, [
              'phone' => 'required|digits_between:7,14|unique:employees,phone,' . $id . ',id,deleted_at,NULL,company_id,' . $company_id . ',country_code,' . $request->country_code.'|unique:users,company_id,' . $company_id,
          ]);
        }

        $this->validate($request, [
          'name' => 'required',
          'employee_code' => 'nullable|unique:employees,employee_code,' . $id . ',id,deleted_at,NULL,company_id,' . $company_id,
          'a_phone' => 'sometimes|nullable|digits_between:7,14',
          'e_phone' => 'sometimes|nullable|digits_between:7,14',
          'designation'=>'required',
      ], $customMessages);
        if ($request->get('email')) {
            if ($user) {
                $this->validate($request, [
                  'email' => 'email|unique:employees,email,' . $id . ',id,deleted_at,NULL,company_id,' . $company_id . '|unique:users,email,' . $user->id . ',id,deleted_at,NULL,company_id,' . $company_id,
              ], $customMessages);
            } else {
                $this->validate($request, [
                  'email' => 'email|unique:employees,email,' . $id . ',id,deleted_at,NULL,company_id,' . $company_id . '|unique:users,company_id,' . $company_id,
              ], $customMessages);
            }
            $employee->email = $request->get('email');
        }
        if ($request->password) {
            $employee->password = $request->get('password');
        }

        $employee->name = $request->get('name');
        $employee->employeegroup = $request->get('employeegroup');
        $employee->employee_code = $request->get('employee_code');
        $phone = $employee->phone;
        $employee->phone = $request->get('phone');
        if (isset($request->englishDate)) {
            $employee->b_date = $request->get('englishDate');
        } else {
            $employee->b_date = $request->get('b_date');
        }
        if (isset($request->englishDoj)) {
            $employee->doj = $request->get('englishDoj');
        } else {
            $employee->doj = $request->get('doj');
        }
        if (isset($request->englishLwd)) {
            $employee->lwd = $request->get('englishLwd');
        } else {
            $employee->lwd = $request->get('lwd');
        }
        $employee->gender = $request->get('gender');
        $employee->local_add = $request->get('local_add');
        $employee->per_add = $request->get('per_add');
        if (Auth::user()->can('employee-status')) {
            $employee->status = $request->get('status');
        }
        $employee->country_code = $request->get('country_code');
        $employee->alt_country_code = $request->get('alt_country_code');
        $employee->e_country_code = $request->get('e_country_code');
        $employee->father_name = $request->get('father_name');
        $employee->a_phone = $request->get('a_phone');
        if ($request->designation) {
            $employee->designation = $request->get('designation');
        }
        if ($request->superior) {
            $employee->superior = $request->get('superior');
        }
        $temprole = $employee->role;
        if ($request->role) {
            $employee->role = $request->role;
        }
        $employee->total_salary = $request->get('total_salary');
        $employee->permitted_leave = $request->get('permitted_leave');
        $employee->acc_holder = $request->get('acc_holder');
        $employee->acc_number = $request->get('acc_number');
        $employee->bank_id = $request->get('bank_id');
        $employee->ifsc_code = $request->get('ifsc_code');
        $employee->pan = $request->get('pan');
        $employee->branch = $request->get('branch');
        $employee->e_name = $request->get('e_name');
        $employee->e_relation = $request->get('e_relation');
        $employee->a_phone = $request->get('a_phone');
        $employee->e_phone = $request->get('e_phone');

        if ($request->file('resume')) {
            $field = 'resume';
            $employee->resume = $this->storeDocs($request, $field);
        }

        if ($request->file('offer_letter')) {
            $field = 'offer_letter';
            $employee->offer_letter = $this->storeDocs($request, $field);
        }

        if ($request->file('joining_letter')) {
            $field = 'joining_letter';
            $employee->joining_letter = $this->storeDocs($request, $field);
        }

        if ($request->file('contract')) {
            $field = 'contract';
            $employee->contract = $this->storeDocs($request, $field);
        }

        if ($request->file('id_proof')) {
            $field = 'id_proof';
            $employee->id_proof = $this->storeDocs($request, $field);
        }


        if ($request->file('image')) {
            $this->validate($request, [
            'image' => 'mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:1000',
          ]);
            $image2 = $request->file('image');
            $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image2->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image2->storeAs('public/uploads/' . $companyName . '/employees/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/employees/' . $new_name);
            $employee->image = $new_name;
            $employee->image_path = $path;
        }
      
        $saved = $employee->save();
        $designation = Designation::where('id', $employee->designation)->first();
        if($designation){
          if($designation->parent_id == 0) {
            $employee->is_admin = 1;
            $employee->superior = NULL;
            $employee->save();
          }
        }

        if ($saved) {
            sendPushNotification_(getFBIDs($company_id), 40, null, array("data_type" => "employee", "employee_id"=>$employee->id,"employee" => $employee, "action" => "update"));

            if ($request->get('status')=='Active') {
                $active=2;
            } else {
                $active=1;
            }

            $user = User::where('company_id', $company_id)->where('id', $employee->user_id)->first();
            if ($user) {
                $user->email = $request->get('email');
                $user->name = $request->get('name');
                $user->phone = $request->get('phone');
                $user->is_active = $active;
                if ($request->password) {
                    $user->password = bcrypt($request->get('password'));
                }
                $user->save();
                $employee->assignUser($user);
                $employee->save();
                if ($request->role) {
                    if ($temprole) {
                        $user->removeRole($temprole);
                    }
                    $user->assignRole($request->role);
                    // $permissions = Permission::all();
                    // foreach($permissions as $permission){
                    //     $data[$permission->name] = ($user->can($permission->name))?'1':'0';
                    // }
                    $role = Role::where('id', $employee->role)->first();
                    if ($role) {
                        $notificationData = array(
                          'company_id' => $company_id,
                          'title' => $role->name,
                          'created_at' => date('Y-m-d H:i:s'),
                          'permissions' => null,
                      );
                        $sendingNotificationData = $notificationData;
                        $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client
                        $sent = sendPushNotification_([$employee->firebase_token], 28, 'Employee Role Updated', $sendingNotificationData);
                    }
                }
            }
        }

        if ($saved && ($employee->status == "Inactive") && !empty($employee->firebase_token)) {
            $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
        }
        if ($saved && ($employee->phone != $phone) && !empty($employee->firebase_token)) {
            $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
        }

        $getEmployeeJuniors = $this->getEmployeeAllJunior($employee->id, $juniors=[]);
        if (!empty($getEmployeeJuniors)) {
            $getJuniorsClients = DB::table('handles')->where('company_id', $company_id)->whereIn('employee_id', $getEmployeeJuniors)->pluck('client_id')->toArray();
        // Session::flash('some_juniors_has_client', 'Some removed parties might still appear as they are assigned to juniors.');
        } else {
            $getJuniorsClients = array();
        }

        if ($request->promoteOption=="replace") {
            DB::table('employee_handle_beats')->where('employee_id', $employee->id)->delete();
            DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->delete();
            DB::table('employee_handle_beats')->where('employee_id', $request->existingusers)->update(['employee_id'=>$employee->id]);
            DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->existingusers)->update(['employee_id'=>$employee->id]);
            Employee::where('company_id', $company_id)->where('superior', $request->existingusers)->update(['superior'=>$employee->id]);
        } elseif ($saved && empty($request->clients)) {
            DB::table('employee_handle_beats')->where('employee_id', $employee->id)->delete();
            //delete future beatplans
            $todayDate = date('Y-m-d');
            $deletedBeatPlans = array();
            $getEmployeePlans = BeatVplan::where('beatvplans.company_id', $company_id)
                                  ->where('beatvplans.employee_id', '=', $id)
                                  ->join('beatplansdetails', 'beatplansdetails.beatvplan_id', '=', 'beatvplans.id')
                                  ->where('beatplansdetails.plandate', '>=', $todayDate)
                                  ->get();
            if (!$getEmployeePlans->isEmpty()) {
                foreach ($getEmployeePlans as $getEmployeePlan) {
                    $planId = $getEmployeePlan->id;
                    $deletedBeatPlans['beatPlansDetailsID'][] = $planId;
                    BeatPlansDetails::findOrFail($planId)->delete();
                }
            }
            $beatVPlanIds = BeatVplan::where('beatvplans.company_id', $company_id)
                          ->where('beatvplans.employee_id', '=', $id)
                          ->get();
            foreach ($beatVPlanIds as $beatVPlanId) {
                if ($beatVPlanId->beatplansdetail->count()==0) {
                    $deletedBeatPlans['beatvPlansID'][] = $beatVPlanId->id;
                    BeatVplan::findOrFail($beatVPlanId->id)->delete();
                }
            }
          
            DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->whereNotIn('client_id', $getJuniorsClients)->delete();

            $employee_id = $employee->id;
            $clients = $request->clients;
            $map_type = 1;

            //sending push notification to app
            if (!empty($employee->firebase_token)) {
                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update", "client_ids" => "", "employee_id" => $employee_id, "deletedBeatPlan"=>$deletedBeatPlans));
            }
        }

        if ((Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()) && $employee->is_admin==1) {
            $clients = Client::where('company_id', $company_id)->pluck('id')->toArray();
            $alreadySet = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->pluck('client_id')->toArray();
            foreach ($clients as $client_id) {
                if (!in_array($client_id, $alreadySet)) {
                    DB::table('handles')->insert([
                  'company_id' => $company_id,
                  'employee_id' => $employee->id,
                  'client_id' => $client_id,
                  'map_type' => 1
              ]);
                }
            }
        }
        if ($saved && !empty($request->clients) && $request->promoteOption!="replace") {
            $deletedBeatPlans = array();
            $employee_id = $employee->id;
            if (getClientSetting()->beat==1) {
                $emp_beats = (explode(',', $request->beatIds));
                foreach ($emp_beats as $emp_beat) {
                    $handle_beat = DB::table('employee_handle_beats')->insert([
                      'employee_id' => $employee_id,
                      'beat_id' => $emp_beat
                  ]);
                }
            }

            DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee->id)->whereNotIn('client_id', $getJuniorsClients)->delete();
          
            if (implode(",", $request->clients) == 0) {
                $clients = Client::where('company_id', $company_id)->pluck('id');
            } else {
                $clients = $request->clients;
            }
            $map_type = 1;
            foreach ($clients as $client_id) {
                $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    DB::table('handles')->insert([
                      'company_id' => $company_id,
                      'employee_id' => $employee_id,
                      'client_id' => $client_id,
                      'map_type' => $map_type
                  ]);
                }
                unset($handle);
            }

            $todayDate = date('Y-m-d');
            $getEmployeePlans = BeatVplan::where('beatvplans.company_id', $company_id)
                                  ->where('beatvplans.employee_id', '=', $id)
                                  ->join('beatplansdetails', 'beatplansdetails.beatvplan_id', '=', 'beatvplans.id')
                                  ->where('beatplansdetails.plandate', '>=', $todayDate)
                                  ->get();
            if ($getEmployeePlans->count()>0) {
                foreach ($getEmployeePlans as $getEmployeePlan) {
                    $beatPlanClients = explode(',', $getEmployeePlan->client_id);
                    $clientDff = array_intersect($request->clients, $beatPlanClients);
                    if (count($clientDff)==0) {
                        $deletedBeatPlans['beatPlansDetailsID'][] = $getEmployeePlan->id;
                        BeatPlansDetails::findOrFail($getEmployeePlan->id)->delete();
                    } else {
                        $beatplans_details = BeatPlansDetails::findOrFail($getEmployeePlan->id);
                        $beatplans_details->client_id = implode(',', $clientDff);
                        if (count($emp_beats)!=0) {
                            $assigned_beats_ids = explode(',', $beatplans_details->beat_id);

                            $unchangedBeats = array_intersect($emp_beats, $assigned_beats_ids);
                            $beatplans_details->beat_id = implode(',', $unchangedBeats);
                            $beattoClients = array();
                            $beatClients = json_decode($beatplans_details->beat_clients);
                            foreach ($beatClients as $beatId=>$beatClient) {
                                if (in_array($beatId, $unchangedBeats)) {
                                    foreach ($beatClient as $assignedClient) {
                                        if (in_array($assignedClient, $clientDff)) {
                                            $beattoClients[$beatId][] = $assignedClient;
                                        }
                                    }
                                }
                            }
                            $beatplans_details->beat_clients = json_encode($beattoClients);
                          
                            unset($assigned_beats_ids);
                        }
                        $beatplans_details->update();
                    }
                }
                $beatVPlanIds = BeatVplan::where('beatvplans.company_id', $company_id)
                              ->where('beatvplans.employee_id', '=', $id)
                              ->get();
                foreach ($beatVPlanIds as $beatVPlanId) {
                    if ($beatVPlanId->beatplansdetail->count()==0) {
                        $deletedBeatPlans['beatvPlansID'][] = $beatVPlanId->id;
                        BeatVplan::findOrFail($beatVPlanId->id)->delete();
                    }
                }
            }
            //sending push notification to app
            if (!empty($employee->firebase_token)) {
              $handling_employee_clients = DB::table('handles')->whereCompanyId($company_id)->whereEmployeeId($employee_id)->pluck('client_id')->toArray();
                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update", "client_ids" => implode(',', $handling_employee_clients), "employee_id" => $employee_id, 'deletedBeatPlan'=>$deletedBeatPlans));
            }
        }

        if($employee->superior){
          $immediateJuniors = Employee::where('superior', $employee->superior)->pluck('id')->toArray();
          $isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;

          sendPushNotification_(getFBIDs($company_id, null, $employee->superior), 40, null, array("data_type" => "employee", "employee_id"=>$employee->superior,"isLowestInChain" => $isLowestInChain, "action" => "update_superior"));

        }

        $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
        $isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;

        sendPushNotification_(getFBIDs($company_id, null, $employee->id), 40, null, array("data_type" => "employee", "employee_id"=>$employee->id,"isLowestInChain" => $isLowestInChain, "action" => "update_superior"));


        return redirect()->route('company.admin.employee', ['domain' => domain()])->with('success', 'Information has been Updated.');
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $employee = Employee::findOrFail($request->employee_id);
        if (Auth::user()->isCompanyEmployee()) {
            if (Auth()->user()->EmployeeId()==$request->employee_id) {
                Session::flash('alert', "Sorry! You can't change your own status");
                return redirect()->route('company.admin.employee', ['domain' => domain()]);
            }
        }
        $childExists = Employee::where('company_id', $company_id)->where('superior', $employee->id)->where('status', 'Active')->first();
        if ($childExists) {
            Session::flash('alert', "Can't change employee's status since it has juniors under it");
            return redirect()->route('company.admin.employee', ['domain' => domain()]);
        }

        if (isset($employee->superior)) {
            $inActiveSuperior = Employee::where('company_id', $company_id)->where('status', 'Inactive')->where('id', $employee->superior)->first();
            if ($inActiveSuperior) {
                Session::flash('alert', "Can't change employee's status since it has inactive senior");
                return redirect()->route('company.admin.employee', ['domain' => domain()]);
            }
        }

        // checking number of active users exceeded
        if ($employee->status == 'Inactive' && $request->status == 'Active') {
            $comp_plan = getCompanyPlan($company_id);
            $comp_plan->users = $this->comp_users;
            $employeescount = Employee::where('company_id', $company_id)->where('status', 'Active')->count();
            if ($employeescount >= $comp_plan->users) {
                Session::flash('alert', 'Employee could not be activated as maximum number has been reached');
                return redirect()->route('company.admin.employee', ['domain' => domain()]);
            }
        }

        $employee->status = $request->status;
        $user_instance = $employee->user;
        $user_instance->update([
        'is_logged_in' => 0
      ]);
        $saved = $employee->save();
        
        if ($saved) {
            sendPushNotification_(getFBIDs($company_id), 40, null, array("data_type" => "employee", "employee_id" =>$employee->id,"employee" => $employee, "action" => "update"));

            if ($employee->status=="Inactive") {
                $logout_fbIDs = DB::table('employees')->where('id', $employee->id)->where(array(array('company_id', $employee->company_id)))->whereNotNull('firebase_token')->pluck('firebase_token');

                $logout_dataPayload = array("data_type" => "employee", "employee" => null, "action" => "logout");
                $logout_sent = sendPushNotification_($logout_fbIDs, 31, null, $logout_dataPayload);
            }
            if ($request->get('status')=='Active') {
                $active=2;
            } else {
                $active=1;
            }

            $user = User::where('id', $employee->user_id)->first();
            if ($user) {
                $user->is_active = $active;
                $user->save();
            }
            if ($active==1) {
                $findUser = User::find($user->id);
                Session::getHandler()->destroy($findUser->session_id);
            }
        }
        if ($saved && ($employee->status == "Archived") && !empty($employee->firebase_token)) {
            $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
        }
        return redirect()->route('company.admin.employee', ['domain' => domain()]);
    }

    public function logout(Request $request)
    {
        $emp_id = $request->empid;
        $instance = Employee::find($emp_id);
        try {
            $user_instance = $instance->user;
            $user_logged_in = $user_instance->is_logged_in;
  
            if ($user_logged_in==0) {
                return response()->json([
          'status' => 200,
          'msg' => 'User is not logged in.',
          'remove_class' => '',
          'class' => 'logged_in'
        ]);
            } else {
                $user_instance->update([
          'is_logged_in' => 0
        ]);

                $logout_fbIDs = DB::table('employees')->where('id', $emp_id)->where(array(array('company_id', $instance->company_id)))->whereNotNull('firebase_token')->pluck('firebase_token');

                $logout_dataPayload = array("data_type" => "employee", "employee" => null, "action" => "logout");
                $logout_sent = sendPushNotification_($logout_fbIDs, 31, null, $logout_dataPayload);
                return response()->json([
          'status' => 200,
          'msg' => 'User logged out.',
          'remove_class' => 'logged_in',
          'class' => 'hidden'
        ]);
            }
        } catch (Exception $e) {
            return response()->json([
        'status' => 400,
        'msg' => $e->getMessage()
      ]);
        }
    }

    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $isManager = Employee::select('employees.id as employeeId', 'employees.is_admin', 'employees.user_id', 'users.id as userId', 'managers.user_id as manager_user_id', 'managers.id as managerId')
          ->leftJoin('users', 'employees.user_id', 'users.id')
          ->leftJoin('managers', 'users.id', 'managers.user_id')
          ->where('employees.company_id', $company_id)
          ->where('employees.id', $request->id)
          ->where('users.id', 'managers.user_id')
          ->first();
        if ($isManager) {
            session()->flash('alert', 'Sorry you can not delete system admin');
            return redirect()->back();
        }
        $empStatus = getEmpActivity($request->id);

        if ($empStatus==true) {
            $employee = Employee::where('company_id', $company_id)->where('id', $request->id)->where('id', '!=', Auth::user()->employeeID())->first();
            if ($employee) {
                $employee_instance = $employee;

                $user = User::find($employee->user_id);
                if($user) $user->update([
                            'deleted_at' => date('Y-m-d H:i:s')
                          ]);

                $deleted = $employee->delete();
                if ($deleted && !empty($employee->firebase_token)) {
                    $msgSent = sendPushNotification_([$employee->firebase_token], 4, null, null);
                }

                sendPushNotification_(getFBIDs($company_id), 40, null, array("data_type" => "employee", "employee_id" =>$employee_instance->id,"employee" => $employee_instance, "action" => "delete"));

                flash()->success('Employee has been deleted.');
            } else {
                session()->flash('alert', "Sorry! You can't delete your own account.");
            }
        } else {
            session()->flash("alert", "This Employee has related datas. Sorry! not deleted.");
        }
        return redirect()->route('company.admin.employee', ['domain' => domain()]);
    }

    public function addClient(Request $request)
    {
        $company_id = config('settings.company_id');
        $employee_id = $request->employee_id;
        $client_list = $request->client_list;
        $map_type = $request->map_type;

        foreach ($client_list as $client_id) {
            $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
            if (empty($handle)) {
                DB::table('handles')->insert([
                  'company_id' => $company_id,
                  'employee_id' => $employee_id,
                  'client_id' => $client_id,
                  'map_type' => $map_type
              ]);
                $employee = Employee::findOrFail($employee_id);
                if ($employee && !empty($employee->firebase_token)) {
                    $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "add", "client_id" => $client_id, "employee_id" => $employee_id));
                }
            }
            unset($handle);
        }
    }

    public function getAllEmployeeSuperior($empId, $superiors)
    {
        $company_id = config('settings.company_id');
        $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
        if (!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))) {
            $superiors[] = $getSuperior->superior;
            $superiors = $this->getAllEmployeeSuperior($getSuperior->superior, $superiors);
        }

        return $superiors;
    }

    public function getEmployeeAllJunior($empId, $juniors)
    {
        $company_id = config('settings.company_id');
        $getJuniors = Employee::where('company_id', $company_id)->where('superior', $empId)->get();
        if (!empty($getJuniors)) {
            foreach ($getJuniors as $getJunior) {
                if (!(in_array($getJunior->id, $juniors))) {
                    $juniors[] = $getJunior->id;
                    $juniors = $this->getEmployeeAllJunior($getJunior->id, $juniors);
                }
            }
        }

        return $juniors;
    }

    public function getEmployeeSuperiors($domain, Request $request)
    {
        $data=[];
        $empSuperiorDesignations=[];
        $authSuperiorDesignations=[];
        $diffArray = [];
        $company_id = config('settings.company_id');
        if (Auth::user()->isCompanyManager()) {
            $chainUsers = Employee::where('company_id', $company_id)->where('status', 'Active')->pluck('id')->toArray();
        } else {
            $chainUsers = Auth::user()->getAllChainUsers(Auth::user()->EmployeeId());
        }
        $empSuperiorDesignations = $this->allDesignationParent($request->designation, $empSuperiorDesignations);
        $authData = Employee::where('id', Auth::user()->EmployeeId())->first();
        $authSuperiorDesignations = $this->allDesignationParent($authData->designation, $authSuperiorDesignations);
        if (Auth::user()->isCompanyManager()) {
        } else {
            array_push($authSuperiorDesignations, $authData->designation);
        }
        $diffArray = array_diff($empSuperiorDesignations, $authSuperiorDesignations);
        $authDesignation = Designation::where('company_id', $company_id)->where('id', $authData->designation)->first();
        $data[$authDesignation->name][$authData->id]['id']=$authData->id;
        $data[$authDesignation->name][$authData->id]['emp_name']=$authData->name;
        $diffArray = array_reverse($diffArray);
        foreach ($diffArray as $subdesign) {
            $designation = Designation::where('company_id', $company_id)->where('id', $subdesign)->first();
            $employees = Employee::select('id', 'name')->where('company_id', $company_id)->where('designation', $subdesign)->whereIn('id', $chainUsers)->where('status', 'Active')->get();
            foreach ($employees as $employee) {
                $data[$designation->name][$employee->id]['id']= $employee->id;
                $data[$designation->name][$employee->id]['emp_name']=$employee->name;
            }
        }
        return $data;
    }

    private function allDesignationParent($id, array $finalresult=[])
    {
        $company_id=config('settings.company_id');
        $result = Designation::where('id', $id)->where('company_id', $company_id)->select('parent_id')->first();
        if ($result && isset($result->parent_id)) {
            $finalresult[]=$result->parent_id;
            $finalresult = $this->allDesignationParent($result->parent_id, $finalresult);
        }
        return $finalresult;
    }

    public function getChainDesignationEmployees($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $authChain = Auth::user()->getChainUsers($request->superior);
        $employees = Employee::where('company_id', $company_id)->whereIn('id', $authChain)->where('designation', $request->designation)->select('id', 'name')->get();
        return $employees;
    }

    public function getsuperiorlist(Request $request)
    {
        $allsuperiors = $this->getAllSuperiors($request->designation, $request->cid);
    
        return response()->json($allsuperiors);
    }

    public function getAllSuperiors($id, $cid)
    {
        $clients = [];
        $company_id = config('settings.company_id');
        $superior = Designation::where('company_id', $company_id)->where('id', $id)->first();
        $allParent = [];
        while ($superior->parent_id != 0) {
            $parent = $superior = Designation::where('company_id', $company_id)->where('id', $superior->parent_id)->first();
            $allParent[$parent->id] = $parent->name;
            $clients[$parent->name] = Employee::where('company_id', $company_id)
            ->where('designation', $parent->id)
            ->where('id', '!=', $cid)
            ->orderBy('created_at', 'desc')
            ->where('status', 'Active')
            ->get();
            $this->getAllSuperiors($parent->id, $cid);
        }
        return array_reverse($clients);
    }

    public function addParties($domain, Request $request, $id)
    {
        $company_id = config('settings.company_id');
        $employee = Employee::find($id);
        // $formSentparties = array_unique($request->party);
        if (!empty($employee)) {
            $extraMsg = '';
            $getSuperiors = $this->getAllEmployeeSuperior($id, $superiors=[]);
            $getEmployeeJuniors = $this->getEmployeeAllJunior($id, $juniors=[]);
            $getJuniorsClients = DB::table('handles')->where('company_id', $company_id)->whereIn('employee_id', $getEmployeeJuniors)->pluck('client_id')->toArray();

            DB::table('handles')->where('company_id', $company_id)->where('employee_id', $id)->whereNotIn('client_id', $getJuniorsClients)->delete();
            $employeeClients = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $id)->pluck('client_id')->toArray();
            // if(!empty($getJuniorsClients)){
            //   $extraMsg = ' Some unchecked parties might still appear as they are assigned to the juniors.';
            // }
            $clientIDs = "";
            $formSentparties = $request->party;

            if (!empty($formSentparties)) {
                $formSentparties = array_unique($formSentparties);
                foreach ($formSentparties as $party_id) {
                    if (!in_array($party_id, $employeeClients)) {
                        DB::table('handles')->insert([
              ['company_id' => $company_id, 'client_id' => $party_id,'employee_id'=>$id,'map_type'=>1]
              ]);
                    }
                    // if(!(empty($getSuperiors))){
            //   foreach($getSuperiors as $getSuperior){
            //     $suphandle = DB::table('handles')->where('company_id', $company_id)->where('employee_id',$getSuperior)->where('client_id', $party_id)->first();
            //     if(empty($suphandle)){
            //       DB::table('handles')->insert([
            //         'company_id' => $company_id,
            //         'employee_id' => $getSuperior,
            //         'client_id' => $party_id,
            //         'map_type' => 1,
            //       ]);
            //     }
            //   }
            // }
                }
                Session::flash('success', 'Parties updated successfully.');
                $clientHandles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $id)->pluck('client_id')->toArray();
                $clientIDs = implode(",", $clientHandles);
                // Log::info('info', array("message"=>print_r($clientIDs,true)));
                $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "employee", "employee_id" => $id,"client_ids" => $clientIDs, "action" => "employee_view_update"));
            } else {
                Session::flash('success', 'Parties cleared successfully.');
                $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "employee", "employee_id" => $id, "action" => "employee_view_update"));
            }
        } else {
            Session::flash('success', 'Invalid Data.');
        }
        return back();
    }
 
    public function getEmployeeHandles($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $data = DB::table('handles')
    ->select('clients.company_name', 'handles.employee_id as empId', 'handles.client_id as clientID', 'clients.id', 'clients.company_id')
    ->leftJoin('clients', 'clients.id', 'handles.client_id')
    ->where('clients.company_id', $company_id)->where('employee_id', $request->employee_id)->get()->toArray();
        return $data;
    }

    public function ajaxUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $employee = Employee::find($request['employee_id']);
        if ($employee) {
            $validator = Validator::make($request->all(), [
              'employee_name'=>'required',
          ]);
            if ($validator->fails()) {
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
            } else {
                if ($request->image) {
                    if ($request->file('image')) {
                        $vimage = Validator::make($request->all(), [
                      'image' => 'mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:1000',
                    ]);
                        $image2 = $request->file('image');
                        $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $image2->getClientOriginalExtension();
                        $new_name = $realname . "-" . time() . '.' . $extension;
                        $image2->storeAs('public/uploads/' . $companyName . '/employees/', $new_name);
                        $path = Storage::url('app/public/uploads/' . $companyName . '/employees/' . $new_name);
                        $employee->image = $new_name;
                        $employee->image_path = $path;
                    }
                }
                $employee->name=$request['employee_name'];
                $employee->father_name = $request['father_name'];
                $employee->gender=$request['gender'];
                if (config('settings.ncal')==1) {
                    if ($request->dob) {
                        $vdob = Validator::make($request->all(), [
                      'dob'=>'date_format:Y-m-d',
                    ]);
                        if ($vdob->fails()) {
                            return response()->json(['code'=>201,'error'=>$vdob->errors()->all()]);
                        }
                        $employee->b_date=getEngDeltaDate($request['dob']);
                    } else {
                        $employee->b_date=null;
                    }
                } else {
                    if ($request->dob) {
                        $vdob = Validator::make($request->all(), [
                      'dob'=>'date_format:Y-m-d',
                    ]);
                        if ($vdob->fails()) {
                            return response()->json(['code'=>201,'error'=>$vdob->errors()->all()]);
                        }
                        $employee->b_date=$request['dob'];
                    } else {
                        $employee->b_date=null;
                    }
                }
                if ($request['status']) {
                    $employee->status=$request['status'];
                    if ($employee->status=="Inactive") {
                        $logout_fbIDs = DB::table('employees')->where('id', $employee->id)->where(array(array('company_id', $employee->company_id)))->whereNotNull('firebase_token')->pluck('firebase_token');

                        $logout_dataPayload = array("data_type" => "employee", "employee" => null, "action" => "logout");
                        $logout_sent = sendPushNotification_($logout_fbIDs, 31, null, $logout_dataPayload);
                    }
                }
                $employee->save();
                sendPushNotification_(getFBIDs($company_id), 40, null, array("data_type" => "employee", "employee_id" =>$employee->id,"employee" => $employee, "action" => "update"));

                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update", "employee_id" => $employee->id));
                return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee]);
            }
        }
    }

    public function ajaxContactUpdate($domain, Request $request)
    {
        $box = $request->all();
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $employee = Employee::find($requests['employee_id']);
        if ($employee) {
            $validator = Validator::make($requests, [
              'email' => 'email|unique:employees,email,' . $requests['employee_id'],
              'a_phone' => 'digits_between:7,14',
              'e_phone' => 'digits_between:7,14',
          ]);
            if ($validator->fails()) {
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
            } else {
                $employee->alt_country_code = $requests['alt_country_code'];
                $employee->e_country_code = $requests['e_country_code'];
                $employee->a_phone=$requests['a_phone'];
                $employee->local_add=$requests['local_address'];
                $employee->per_add=$requests['permanent_address'];
                $employee->e_name=$requests['e_name'];
                $employee->e_phone=$requests['e_phone'];
                $employee->e_relation=$requests['e_relation'];
                $employee->save();
                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update","employee_id" => $employee->id));
                return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee]);
            }
        }
    }

    public function ajaxCompanyUpdate($domain, Request $request)
    {
        $box = $request->all();
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $employee = Employee::find($requests['employee_id']);
        if ($employee) {
          $validator = Validator::make($requests, [
            'total_salary'=>'integer',
            'employee_code' => 'nullable|unique:employees,employee_code,' . $employee->id . ',id,deleted_at,NULL,company_id,' . $company_id,
          ]);
          if ($validator->fails()) {
            return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
          } else {
            $employee->employee_code=$requests['employee_code'];
            $employee->total_salary=$requests['total_salary'];
            $employee->permitted_leave=$requests['permitted_leave'];

            if (config('settings.ncal')==1) {
              if ($requests['doj']) {
                $vdoj = Validator::make($requests, [
                          'doj'=>'date_format:Y-m-d',
                        ]);
                if ($vdoj->fails()) {
                  return response()->json(['code'=>201,'error'=>$vdoj->errors()->all()]);
                }
                $employee->doj = getEngDeltaDate($requests['doj']);
                $formatted_doj = getDeltaDate($requests['doj']);
              } else {
                $employee->doj= null;
                $formatted_doj = null;
              }
            } else {
              if ($requests['doj']) {
                $vdoj = Validator::make($requests, [
                          'doj'=>'date_format:Y-m-d',
                          ]);
                if ($vdoj->fails()) {
                  return response()->json(['code'=>201,'error'=>$vdoj->errors()->all()]);
                }
                $employee->doj=$requests['doj'];
                $formatted_doj = getDeltaDate($requests['doj']);
              } else {
                $employee->doj=null;
                $formatted_doj = null;
              }
            }

            $temprole = $employee->role;
            if (isset($requests['role'])) {
              $employee->role = $requests['role'];
              $user = User::where('company_id', $company_id)->where('id', $employee->user_id)->first();
              if ($temprole) {
                $user->removeRole($temprole);
              }
              $user->assignRole($requests['role']);
              $permissions = Permission::all();
              // foreach ($permissions as $permission) {
              //   $data[$permission->name] = ($user->can($permission->name))?'1':'0';
              // }
              $role = Role::where('id', $employee->role)->first();
              if ($role) {
                $notificationData = array(
                  'company_id' => $company_id,
                  'title' => $role->name,
                  'created_at' => date('Y-m-d H:i:s'),
                  'permissions' => null//$data,
                );
                $sendingNotificationData = $notificationData;
                $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client
                $sent = sendPushNotification_([$employee->firebase_token], 27,'Roles Updated',$sendingNotificationData);
              }
            }
            if (config('settings.ncal')==1) {
              if ($requests['lwd']) {
                $vlwd = Validator::make($requests, [
                          'lwd'=>'date_format:Y-m-d',
                        ]);
                if ($vlwd->fails()) {
                  return response()->json(['code'=>201,'error'=>$vlwd->errors()->all()]);
                }
                $employee->lwd=getEngDeltaDate($requests['lwd']);
                $formatted_lwd = getDeltaDate($requests['lwd']);
              } else {
                $employee->lwd=null;
                $formatted_lwd = null;
              }
            } else {
              if ($requests['lwd']) {
                $vlwd = Validator::make($requests, [
                          'lwd'=>'date_format:Y-m-d',
                        ]);
                if ($vlwd->fails()) {
                  return response()->json(['code'=>201,'error'=>$vlwd->errors()->all()]);
                }
                $employee->lwd=$requests['lwd'];
                $formatted_lwd = getDeltaDate($requests['lwd']);
              } else {
                $employee->lwd= null;
                $formatted_lwd = null;
              }
            }
            if (isset($requests['employee_group'])) {
              $employee->employeegroup=$requests['employee_group'];
            } else {
              $employee->employeegroup=null;
            }
            $employee->save();
            $employee->formatted_doj = $formatted_doj;
            $employee->formatted_lwd = $formatted_lwd;
            if ($employee->total_salary) {
              $employee->total_salary= config('settings.currency_symbol').' '.number_format((float)$employee->total_salary, 2);
            } else {
              $employee->total_salary=null;
            }

            $role = Role::where('id', $employee->role)->first();
            $groupname = EmployeeGroup::where('id', $employee->employeegroup)->first();
            if (!$groupname) {
                $groupname['name']='N/A';
            }
            $designationname = Designation::where('id', $employee->designation)->first();
            $superior = Employee::where('id', $employee->superior)->first();
            $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update","employee_id" => $employee->id));

            if($employee->superior){
              $immediateJuniors = Employee::where('superior', $employee->superior)->pluck('id')->toArray();
              $isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;
    
              sendPushNotification_(getFBIDs($company_id, null, $employee->superior), 40, null, array("data_type" => "employee", "employee_id"=>$employee->superior,"isLowestInChain" => $isLowestInChain, "action" => "update_superior"));
    
            }

            $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
            $isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;
  
            sendPushNotification_(getFBIDs($company_id, null, $employee->id), 40, null, array("data_type" => "employee", "employee_id"=>$employee->id,"isLowestInChain" => $isLowestInChain, "action" => "update_superior"));
    

            return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee,'groupname'=>$groupname,'designationname'=>$designationname,'superior'=>$superior,'role'=>$role]);
          }
        }
    }

    public function ajaxbankUpdate($domain, Request $request)
    {
        $box = $request->all();
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $employee = Employee::find($requests['employee_id']);
        if ($employee) {
            $validator = Validator::make($requests, [
            'acc_number'=>'digits_between:7,50',
          ]);
            if ($validator->fails()) {
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
            } else {
                $employee->acc_holder=$requests['acc_holder'];
                $employee->acc_number=$requests['acc_number'];
                $employee->bank_id = $requests['bank_id'];
                $employee->ifsc_code=$requests['ifsc_code'];
                $employee->pan=$requests['pan'];
                $employee->branch=$requests['branch'];
                $employee->save();
                if ($requests['bank_id']) {
                    $bank = Bank::where('id', $requests['bank_id'])->first();
                    $employee->bank_name=$bank->name;
                }
                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update","employee_id" => $employee->id));
                return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee]);
            }
        }
    }

    public function ajaxDocumentUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $employee = Employee::find($request->employee_id);
        if ($employee) {
            if ($request->file('resume')) {
                $field = 'resume';
                $employee->resume = $this->storeDocs($request, $field);
            }

            if ($request->file('offer_letter')) {
                $field = 'offer_letter';
                $employee->offer_letter = $this->storeDocs($request, $field);
            }

            if ($request->file('joining_letter')) {
                $field = 'joining_letter';
                $employee->joining_letter = $this->storeDocs($request, $field);
            }

            if ($request->file('contract')) {
                $field = 'contract';
                $employee->contract = $this->storeDocs($request, $field);
            }

            if ($request->file('id_proof')) {
                $field = 'id_proof';
                $employee->id_proof = $this->storeDocs($request, $field);
            }
            $employee->save();
            if ($employee->resume) {
                $employee->resume_url = '<a href="'. URL::asset('cms'.$employee->resume) .'">View/download</a> <i class="fa fa-trash btn-red" data-type="resume"></i>';
            }
            if ($employee->offer_letter) {
                $employee->offer_letter_url = '<a href="'. URL::asset('cms'.$employee->offer_letter) .'">View/download</a> <i class="fa fa-trash btn-red" data-type="offer_letter"></i>';
            }
            if ($employee->joining_letter) {
                $employee->joining_letter_url = '<a href="'. URL::asset('cms'.$employee->joining_letter) .'">View/download</a> <i class="fa fa-trash btn-red" data-type="joining_letter"></i>';
            }
            if ($employee->contract) {
                $employee->contract_url = '<a href="'. URL::asset('cms'.$employee->contract) .'">View/download</a> <i class="fa fa-trash btn-red" data-type="contract"></i>';
            }
            if ($employee->id_proof) {
                $employee->id_proof_url = '<a href="'. URL::asset('cms'.$employee->id_proof) .'">View/download</a> <i class="fa fa-trash btn-red" data-type="id_proof"></i>';
            }
            $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update", "employee_id" => $employee->id));
            return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee]);
        }
    }

    public function ajaxAccountUpdate($domain, Request $request)
    {
        $box = $request->all();
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $employee = Employee::find($requests['employee_id']);
        if ($employee) {
            $vemail = Validator::make($requests, [
              'email' => 'sometimes|nullable|email|unique:employees,email,'.$requests['employee_id'].',id,deleted_at,NULL,company_id,' . $company_id ,
              'phone' => 'required|digits_between:7,14|unique:employees,phone,'.$requests['employee_id'].',id,deleted_at,NULL,company_id,' . $company_id ,
          ]);
            if ($vemail->fails()) {
                return response()->json(['code'=>201,'error'=>$vemail->errors()->all()]);
            } else {
                $employee->email=$requests['email'];
                $employee->phone=$requests['phone'];
                if ($requests['password']) {
                    if ($requests['password']) {
                        $vpassword = Validator::make($requests, [
                        'password' => 'required|min:8',
                    ]);
                    }
                    if ($vpassword->fails()) {
                        return response()->json(['code'=>201,'error'=>$vpassword->errors()->all()]);
                    }
                    if ($employee->status=='Active') {
                        $active=2;
                    } else {
                        $active=1;
                    }
                    $user = User::where('id', $employee->user_id)->first();
                    $user->name = $employee->name;
                    $user->phone=$requests['phone'];
                    $user->email = $requests['email'];
                    $user->is_active = $active;
                    $user->company_id = $company_id;
                    if ($requests['password']) {
                        $user->password = bcrypt($requests['password']);
                    }
                    $user->save();
                    $employee->password=$requests['password'];
                }
                $employee->save();
                $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "employee_update", "employee_id" => $employee->id));
                return response()->json(['code'=>200,'success'=>"Updated Successfully",'empData'=>$employee]);
            }
        }
    }

    public function getAttendances(Request $request)
    {
        $company_id = config('settings.company_id');

        //for Holidays section
        $data['holidays'] = [];
        $holidays = Holiday::where('company_id', $company_id)->where('start_date', '<=', $request->engLastDate)->where('end_date', '>=', $request->engFirstDate)->orderBy('start_date', 'ASC')->get();
        $data=$this->ajaxEvents($request->engFirstDate, $holidays);
        if (isset($data['holidays'])) {
            $i = count($data['holidays']);
        } else {
            $data['holidays'] = [];
            $i=0;
        }
        $attendances = Attendance::where('company_id', $company_id)->select('id', 'adate')->groupBy('adate')->where('employee_id', $request->id)->where('adate', '>=', $request->engFirstDate)->where('adate', '<=', $request->engLastDate)->orderBy('adate', 'ASC')->get();

        // for present section
        $data['presents'] = [];
        foreach ($attendances as $attendance) {
            if ($attendance->adate>=$request->engCMFDate && $attendance->adate<=$request->engCMLDate) {
                $data['presents'][] = $attendance->id;
            }
            $data['holidays'][$i]['id'] = $attendance->id;
            $data['holidays'][$i]['name'] = 'Present';
            $data['holidays'][$i]['description'] = '';
            $data['holidays'][$i]['start_date'] = $attendance->adate;
            $data['holidays'][$i]['end_date'] = $attendance->adate;
            $data['holidays'][$i]['ostart_date'] = $attendance->adate;
            $data['holidays'][$i]['oend_date'] = $attendance->adate;
            $data['holidays'][$i]['color'] = "#48c065";
            $i++;
        }
        //for absent section
        $today_date = Carbon::now()->addDays(1)->format('Y-m-d');
        $current_date = Carbon::now()->format('Y-m-d');
        $holiday_list =[];
        $beforeTodays = [];
        // $employeeFirstAttenDay =  Attendance::where('company_id',$company_id)->where('employee_id',$request->id)->orderBy('adate','ASC')->first();
        $employeeFirstAttenDay = Employee::where('company_id', $company_id)->where('id', $request->id)->first();
        $empCDate= Carbon::parse($employeeFirstAttenDay->created_at)->format('Y-m-d');
        if (isset($empCDate)) {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $empCDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        } else {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $request->engFirstDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        }
        foreach ($holidaysDatesLatestDays as $holiday) {
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            while ($start_date<=$end_date) {
                $holiday_list[] = $start_date->format('Y-m-d');
                $start_date = $start_date->addDays(1);
            }
        }

        if (isset($empCDate)) {
            $start_date = Carbon::parse($empCDate);
        } else {
            $start_date = Carbon::parse($today_date);
        }

        $end_date = Carbon::now();
        while ($start_date <= $end_date) {
            $beforeTodays[] = $start_date->format('Y-m-d');
            $start_date = $start_date->addDays(1);
        }
        foreach ($holidays as $holiday) {
            $end_date = Carbon::parse($holiday->end_date);
            // $data['nextday_end'][$holiday->id] = $end_date->addDays(1);
        }
        foreach ($holiday_list as $holiday) {
            if (($key = array_search($holiday, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach ($attendances as $attendance) {
            if (($key = array_search($attendance->adate, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }

        $data['absents'] = [];
        foreach ($beforeTodays as $absentdate) {
            if ($absentdate>=$request->engFirstDate && $absentdate<=$request->engLastDate) {
                if ($absentdate>=$request->engCMFDate && $absentdate<=$request->engCMLDate) {
                    $data['absents'][]=$absentdate;
                }
                $data['holidays'][$i]['id'] = $absentdate;
                $data['holidays'][$i]['name'] = 'Absent';
                $data['holidays'][$i]['description'] = '';
                $data['holidays'][$i]['start_date'] = $absentdate;
                $data['holidays'][$i]['end_date'] = $absentdate;
                $data['holidays'][$i]['ostart_date'] = $absentdate;
                $data['holidays'][$i]['oend_date'] = $absentdate;
                $data['holidays'][$i]['color'] = "#f58641";
                $i++;
            }
        }

        //Count Sections
        $hollys = Holiday::where('company_id', $company_id)->where('start_date', '<=', $request->engCMLDate)->where('end_date', '>=', $request->engCMFDate)->orderBy('start_date', 'ASC')->get();
        $data['noOfHollydays']=[];
        foreach ($hollys as $holly) {
            if ($holly->start_date<$request->engCMFDate) {
                $startDate=Carbon::parse($request->engCMFDate);
            } else {
                $startDate = Carbon::parse($holly->start_date);
            }
            if ($holly->end_date>$request->engCMLDate) {
                $endDate = Carbon::parse($request->engCMLDate);
            } else {
                $endDate = Carbon::parse($holly->end_date);
            }
            while ($startDate<=$endDate) {
                $data['noOfHollydays'][$startDate->format('Y-m-d')]=$endDate->format('Y-m-d');
                $startDate = $startDate->addDays(1);
            }
        }
        $data['hollyDays'] = count($data['noOfHollydays']);
        $data['WOff']= $hollys->where('name', 'Weekly Off')->count();
        $data['maxCount'] = Holiday::where('company_id', $company_id)->where('start_date', '>=', $request->engFirstDate)->where('end_date', '<=', $request->engLastDate)->orderBy('start_date', 'ASC')->get()->count();
        $data['year'] = $request->getYear;
        $data['month'] = $request->getMonth;
        return $data;
    }

    public function getAttendancesCount(Request $request)
    {
        $company_id = config('settings.company_id');
        $attendances= Attendance::where('company_id', $company_id)->select('id', 'adate', 'employee_id')->groupBy('adate')->where('employee_id', $request->id)->where('adate', '>=', $request->engCMFDate)->where('adate', '<=', $request->engCMLDate)->orderBy('adate', 'ASC')->get();

        $today_date = Carbon::now()->addDays(1)->format('Y-m-d');
        $current_date = Carbon::now()->format('Y-m-d');
        $holiday_list =[];
        $beforeTodays = [];
        $data['absents']=[];


        $employeeFirstAttenDay = Employee::where('company_id', $company_id)->where('id', $request->id)->first();
        $empCDate= Carbon::parse($employeeFirstAttenDay->created_at)->format('Y-m-d');
        if (isset($empCDate)) {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $empCDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        } else {
            $holidaysDatesLatestDays = Holiday::where('company_id', $company_id)->where('start_date', '>', $request->engFirstDate)->where('start_date', '<=', $current_date)->orderBy('start_date', 'ASC')->get();
        }
        foreach ($holidaysDatesLatestDays as $holiday) {
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            while ($start_date<=$end_date) {
                $holiday_list[] = $start_date->format('Y-m-d');
                $start_date = $start_date->addDays(1);
            }
        }

        if (isset($empCDate)) {
            $start_date = Carbon::parse($empCDate);
        } else {
            $start_date = Carbon::parse($today_date);
        }




        // $employeeFirstAttenDay =  Attendance::where('company_id',$company_id)->where('employee_id',$request->id)->orderBy('adate','ASC')->first();
        // if(isset($employeeFirstAttenDay->adate)){
        // $holidaysDatesLatestDays = Holiday::where('company_id',$company_id)->where('start_date','>',$employeeFirstAttenDay->adate)->where('start_date','<=',$current_date)->orderBy('start_date','ASC')->get();
        // } else{
        //     $holidaysDatesLatestDays = Holiday::where('company_id',$company_id)->where('start_date','>',$request->engCMFDate)->where('start_date','<=',$current_date)->orderBy('start_date','ASC')->get();
        // }
        // foreach($holidaysDatesLatestDays as $holiday){
        //     $start_date = Carbon::parse($holiday->start_date);
        //     $end_date = Carbon::parse($holiday->end_date);
        //     while($start_date<=$end_date){
        //         $holiday_list[] = $start_date->format('Y-m-d');
        //         $start_date = $start_date->addDays(1);
        //     }
        // }
        // if(isset($employeeFirstAttenDay->adate)){
        //     $start_date = Carbon::parse($employeeFirstAttenDay->adate);
        // }else{
        //     $start_date = Carbon::parse($today_date);
        // }

        $end_date = Carbon::now();
        while ($start_date <= $end_date) {
            $beforeTodays[] = $start_date->format('Y-m-d');
            $start_date = $start_date->addDays(1);
        }
        foreach ($holiday_list as $holiday) {
            if (($key = array_search($holiday, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach ($attendances as $attendance) {
            if (($key = array_search($attendance->adate, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach ($beforeTodays as $absentdate) {
            if ($absentdate>=$request->engCMFDate && $absentdate<=$request->engCMLDate) {
                $data['absents'][]=$absentdate;
            }
        }
        $hollys = Holiday::where('company_id', $company_id)->where('start_date', '<=', $request->engCMLDate)->where('end_date', '>=', $request->engCMFDate)->orderBy('start_date', 'ASC')->get();
        $data['noOfHollydays']=[];
        foreach ($hollys as $holly) {
            if ($holly->start_date<$request->engCMFDate) {
                $startDate=Carbon::parse($request->engCMFDate);
            } else {
                $startDate = Carbon::parse($holly->start_date);
            }
            if ($holly->end_date>$request->engCMLDate) {
                $endDate = Carbon::parse($request->engCMLDate);
            } else {
                $endDate = Carbon::parse($holly->end_date);
            }
            while ($startDate<=$endDate) {
                $data['noOfHollydays'][$startDate->format('Y-m-d')]=$endDate->format('Y-m-d');
                $startDate = $startDate->addDays(1);
            }
        }
        $data['hollyDays'] = count($data['noOfHollydays']);
        $data['WOff']= $hollys->where('name', 'Weekly Off')->count();
        $data['presents'] = $attendances->count();
        return $data;
    }

    public function removeDoc(Request $request)
    {
        $company_id = config('settings.company_id');
        $employee= Employee::where('company_id', $company_id)->where('id', $request->emp_id)->first();
        if ($employee) {
            if ($request->doc_type=="resume") {
                $employee->resume=null;
            } elseif ($request->doc_type=="offer_letter") {
                $employee->offer_letter=null;
            } elseif ($request->doc_type=="joining_letter") {
                $employee->joining_letter=null;
            } elseif ($request->doc_type=="contract") {
                $employee->contract=null;
            } elseif ($request->doc_type=="id_proof") {
                $employee->id_proof=null;
            }
            $employee->save();
            $data['doc_type']=$request->doc_type;
            $data['result']=true;
        } else {
            $data['result']=false;
        }
        return $data;
    }

    private function ajaxEvents($engFirstDate, $holidays)
    {
        $FirstRowFirstDate = $engFirstDate;
        $FirstRowLastDate = Carbon::parse($engFirstDate)->addDays(6)->format('Y-m-d');
        $SecondRowFirstDate = Carbon::parse($engFirstDate)->addDays(7)->format('Y-m-d');
        $SecondRowLastDate = Carbon::parse($engFirstDate)->addDays(13)->format('Y-m-d');
        $ThirdRowFirstDate = Carbon::parse($engFirstDate)->addDays(14)->format('Y-m-d');
        $ThirdRowLastDate = Carbon::parse($engFirstDate)->addDays(20)->format('Y-m-d');
        $FourthRowFirstDate = Carbon::parse($engFirstDate)->addDays(21)->format('Y-m-d');
        $FourthRowLastDate = Carbon::parse($engFirstDate)->addDays(27)->format('Y-m-d');
        $FifthRowFirstDate = Carbon::parse($engFirstDate)->addDays(28)->format('Y-m-d');
        $FifthRowLastDate = Carbon::parse($engFirstDate)->addDays(34)->format('Y-m-d');
        $SixthRowFirstDate = Carbon::parse($engFirstDate)->addDays(35)->format('Y-m-d');
        $SixthRowLastDate = Carbon::parse($engFirstDate)->addDays(41)->format('Y-m-d');
        $i=0;
        $data = [];
        foreach ($holidays as $holiday) {
            if ($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
                $data['holidays'][$i]['end_date'] = $FirstRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FirstRowFirstDate && $holiday->start_date<=$FirstRowLastDate && $holiday->end_date>=$FirstRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $FirstRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }

            // Second row
            if ($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
                $data['holidays'][$i]['end_date'] = $SecondRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$SecondRowFirstDate && $holiday->start_date<=$SecondRowLastDate && $holiday->end_date>=$SecondRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $SecondRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }

            // Third Row
            if ($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
                $data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$ThirdRowFirstDate && $holiday->start_date<=$ThirdRowLastDate && $holiday->end_date>=$ThirdRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }

            //Fourth Row
            if ($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $FourthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FourthRowFirstDate && $holiday->start_date<=$FourthRowLastDate && $holiday->end_date>=$FourthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $FourthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }
            //Fifth Row
            if ($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $FifthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FifthRowFirstDate && $holiday->start_date<=$FifthRowLastDate && $holiday->end_date>=$FifthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $FifthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }

            // Sixth row
            if ($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $SixthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$SixthRowFirstDate && $holiday->start_date<=$SixthRowLastDate && $holiday->end_date>=$SixthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $SixthRowLastDate;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            } elseif ($holiday->start_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate) {
                $data['holidays'][$i]['id'] = $holiday->id;
                $data['holidays'][$i]['name'] = $holiday->name;
                $data['holidays'][$i]['description'] = $holiday->description;
                $data['holidays'][$i]['start_date'] = $holiday->start_date;
                $data['holidays'][$i]['end_date'] = $holiday->end_date;
                $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
                $data['holidays'][$i]['oend_date'] = $holiday->end_date;
                $data['holidays'][$i]['color'] = "#F96954";
                $i++;
            }
            // End Of rows data
        }

        return $data;
    }

    private function getSubDesignations($id, $SubDesignation)
    {
        $company_id = config('settings.company_id');
        $subdesignations = Designation::where('company_id', $company_id)->where('parent_id', $id)->get();
        foreach ($subdesignations as $designation) {
            $SubDesignation[]=$designation->id;
            $subdesignationss = Designation::where('company_id', $company_id)->where('parent_id', $designation->id)->get();
            if ($subdesignationss->count()>0) {
                $SubDesignation = $this->getSubDesignations($designation->id, $SubDesignation);
            }
        }
        return $SubDesignation;
    }

    public function getsuperiorparties(Request $request)
    {
        $company_id = config('settings.company_id');
        $superior = $request->superior;
        $listType = $request->listType;
        $time = time();
        $superiorHandles = DB::table('handles')->where('employee_id', $superior)->where('company_id', $company_id)->distinct('client_id')->pluck('client_id')->toArray();
        $links = array();
        try {
            $empID = $request->emp_id;
            $links = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $request->id)->pluck('client_id')->toArray();
        } catch (\Exception $e) {
            $empID = null;
        }
        if ($empID) {
            $employee_handles = $superiorHandles->where('employee_id', $empID);
            array_merge($superiorHandles, $employee_handles);
        }
        if (!(empty($superiorHandles))) {
            if ($listType==0) {
                $beat_clients = Client::select('clients.company_name as clients', 'clients.id as client_id', 'beats.name as beat', 'beats.id as beat_id')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereIn('clients.id', $superiorHandles)
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('beats.id', 'asc')
              ->get();
                // $beat_clients = Client::select('clients.company_name as clients', 'clients.id as client_id','beats.name as beat','beats.id as beat_id')
                //                   ->leftJoin('beat_client','clients.id','beat_client.client_id')
                //                   ->leftJoin('beats','beats.id','beat_client.beat_id')
                //                   ->where('clients.company_id', $company_id)
                //                   ->where('clients.status', 'Active')
                //                   ->whereIn('clients.id', $superiorHandles)
                //                   ->orderBy('beats.name', 'desc')
                //                   ->get();
                foreach ($beat_clients as $beat_client) {
                    if ($beat_client->beat_id!=0 && isset($beat_client->beat_id)) {
                        $beats_list[$beat_client->beat_id]['name']=$beat_client->beat;
                        $beats_list[$beat_client->beat_id]['id']=$beat_client->beat_id;
                        $beats_list[$beat_client->beat_id]['clients'][$beat_client->client_id]=$beat_client->clients;
                    } elseif ($beat_client->beat_id==0 || !isset($beat_client->beat_id)) {
                        $beats_list[$time]['name']='Unspecified';
                        $beats_list[$time]['id']='0';
                        $beats_list[$time]['clients'][$beat_client->client_id]=$beat_client->clients;
                    }
                }
                ksort($beats_list);

                return $beats_list;
            } else {
                $party_wise_clients = Client::select('clients.company_name as clients', 'clients.id as client_id', 'partytypes.name as partytype', 'partytypes.id as partytype_id', 'clients.client_type')
              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
              ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
              ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
              ->where('clients.company_id', $company_id)
              ->where('clients.status', 'Active')
              ->whereIn('clients.id', $superiorHandles)
              ->whereNotIn('clients.id', $links)
              ->whereNULL('clients.deleted_at')
              ->distinct()
              ->orderBy('partytypes.parent_id', 'desc')
              ->get();
                // $party_wise_clients = Client::select('clients.company_name as clients', 'clients.id as client_id','partytypes.name as partytype','partytypes.id as partytype_id', 'clients.client_type')
                //                 ->leftJoin('partytypes','clients.client_type','partytypes.id')
                //                 ->where('clients.company_id', $company_id)
                //                 ->where('clients.status', 'Active')
                //                 ->whereIn('clients.id', $superiorHandles)
                //                 ->orderBy('partytypes.name', 'desc')
                //                 ->get();
                foreach ($party_wise_clients as $party_wise_client) {
                    if ($party_wise_client->client_type!=0 && isset($party_wise_client->client_type)) {
                        $party_list[$party_wise_client->partytype_id]['name']=$party_wise_client->partytype;
                        $party_list[$party_wise_client->partytype_id]['id']=$party_wise_client->partytype_id;
                        $party_list[$party_wise_client->partytype_id]['clients'][$party_wise_client->client_id]=$party_wise_client->clients;
                    } elseif ($party_wise_client->client_type==0 || !isset($party_wise_client->client_type)) {
                        $party_list[$time]['name']='Unspecified';
                        $party_list[$time]['id']='0';
                        $party_list[$time]['clients'][$party_wise_client->client_id]=$party_wise_client->clients;
                    }
                }
                return $party_list;
            }
        } else {
            return null;
        }
    }

    private function storeDocs($request, $field)
    {
        $vdocs = Validator::make($request->all(), [
        $field => 'mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:5000',
      ]);
        if ($vdocs->fails()) {
            return response()->json(['code'=>201,'error'=>$vdocs->errors()->all()]);
        }
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $image2 = $request->file($field);
        $realname = pathinfo($request->file($field)->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image2->getClientOriginalExtension();
        $new_name = $realname . "-" . time() . '.' . $extension;
        $image2->storeAs('public/uploads/' . $companyName . '/employees/'.$field.'/', $new_name);
        $path = Storage::url('app/public/uploads/' . $companyName . '/employees/'.$field.'/'. $new_name);
        return $path;
    }

    public function dayremark(Request $request)
    {
        $remarkId = $request->id;
        $getDayRemarkInstance = DayRemark::findOrFail($remarkId);
        return view('company.employees.dayremark', compact('getDayRemarkInstance'));
    }

    public function empActivityTable(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'completion_datetime',
            2 =>'title',
            3 => 'PartyName',
            4 => 'TypeName',
            5 => 'AssignedByName',
            6 => 'AssignedToName',
            7 => 'completed',
            8 => 'action',
        );
        $empId = $request->create_assign_id;
        $company_id = config('settings.company_id');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
               
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
        } else {
            $search = null;
        }
        $allSup = [];
        $totalData = Activity::where('company_id', $company_id)->where(function ($q) use ($empId) {
            $q= $q->where('created_by', $empId)->orWhere('assigned_to', $empId);
        })->get()->count();
        
        $totalFiltered = $totalData;
        $activities = Activity::select('activities.*', 'addedBy_tbl.id as AddedById', 'addedBy_tbl.name as AssignedByName', 'approvedBy_tbl.id as ApprovedById', 'approvedBy_tbl.name as AssignedToName', 'clients.id as ClientId', 'clients.company_name as PartyName', 'activity_types.id as type', 'activity_types.name as TypeName', 'activity_priorities.id as PriorityId', 'activity_priorities.name as PriorityName')
                  ->leftJoin('employees as approvedBy_tbl', 'activities.assigned_to', 'approvedBy_tbl.id')
                  ->leftJoin('employees as addedBy_tbl', 'activities.created_by', 'addedBy_tbl.id')
                  ->leftJoin('clients', 'activities.client_id', 'clients.id')
                  ->leftJoin('activity_types', 'activities.type', 'activity_types.id')
                  ->leftJoin('activity_priorities', 'activities.priority', 'activity_priorities.id')
                  ->where('activities.company_id', $company_id)->where(function ($q) use ($empId) {
                      $q= $q->where('activities.created_by', $empId)->orWhere('activities.assigned_to', $empId);
                  });
        if ($search) {
            $activities = $activities->where(function ($q3) use ($search) {
                $q3 = $q3->orWhere('activities.start_datetime', 'LIKE', "%{$search}%")
                    ->orWhere('activities.title', 'LIKE', "%{$search}%")
                    ->orWhere('clients.company_name', 'LIKE', "%{$search}%")
                    ->orWhere('activity_types.name', 'LIKE', "%{$search}%")
                    ->orWhere('addedBy_tbl.name', 'LIKE', "%{$search}%")
                    ->orWhere('approvedBy_tbl.name', 'LIKE', "%{$search}%");
            });
        }
                  
        $totalFiltered = $activities->get()->count();
        $activities = $activities->orderBy('activities.id', $dir)->orderBy($order, $dir)->offset($start)
                    ->limit($limit)
                    ->get();

        if (Auth::user()->isCompanyManager()) {
            $allSup = [];
        } else {
            $allSup = [];
            $allSup = Auth::user()->getUpChainUsers($empId, $allSup);
            array_pop($allSup);
            $empAdmins = Employee::where('company_id', $company_id)->select('id')->where('is_admin', 1)->get();
            foreach ($empAdmins as $emp) {
                array_push($allSup, $emp->id);
            }
        }
        $data = array();
        if (!empty($activities)) {
            $i = $start;
            foreach ($activities as $activity) {
                $show =  domain_route('company.admin.activities.show', [$activity->id]);
                $edit =  domain_route('company.admin.activities.edit', [$activity->id]);
                $delete =  domain_route('company.admin.activities.destroy', [$activity->id]);

                $nestedData['id'] = ++$i;
                $nestedData['completion_datetime'] = isset($activity->start_datetime)?getDeltaDate(date('Y-m-d', strtotime($activity->start_datetime))).' '.date('H:i', strtotime($activity->start_datetime)) : null;

                $nestedData['title'] = $activity->title;

                $client = "<span class='hidden'>{$activity->client_id}</span>";
                if ($activity->client_id!=null) {
                    if (isset($activity->PartyName)) {
                        $access = \DB::table('handles')->where('company_id', $company_id)->where('employee_id', Auth::user()->EmployeeId())->where('client_id', $activity->client_id)->first();
                        if (isset($access)) {
                            $clientShow = domain_route('company.admin.client.show', [$activity->client_id]);
                            $client = "<a href='{$clientShow}'>{$activity->PartyName}</a>";
                        } else {
                            $client = "<a href='#' class='alert_party_model'>{$activity->PartyName}</a>";
                        }
                    }
                }

                $nestedData['PartyName'] = $client;

                $nestedData['TypeName'] = isset($activity->TypeName)?$activity->TypeName:null;

                if ($activity->created_by==0) {
                    $assigned_by = ucfirst(Auth::user()->name);
                } elseif (isset($activity->createdByEmployee->name)) {
                    if (in_array($activity->created_by, $allSup)) {
                        $assigned_by = "<a href='#' class='alert-user-modal' datasalesman='{$activity->AssignedByName}'>{$activity->AssignedByName}</a>";
                    } else {
                        $showEmp = domain_route('company.admin.employee.show', [$activity->AddedById]);
                        $assigned_by = "<a href='{$showEmp}' datasalesman='{$activity->AssignedByName}'>{$activity->AssignedByName}</a>";
                    }
                } else {
                    $assigned_by = "<span hidden>{$activity->created_by}</span>";
                }

                $nestedData['AssignedByName'] = $assigned_by;

                if ($activity->assigned_to==0) {
                    $assigned_to = 'ALL';
                } else {
                    if (isset($activity->AssignedToName)) {
                        if (in_array($activity->assigned_to, $allSup)) {
                            $assigned_to = "<a href='#' class='alert-user-modal'>{$activity->AssignedToName}</a>";
                        } else {
                            $showAsignTO = domain_route('company.admin.employee.show', [$activity->assigned_to]);
                            $assigned_to = "<a href='{$showAsignTO}'>{$activity->AssignedToName}</a>";
                        }
                    } else {
                        $assigned_to = "<span hidden>{$activity->assigned_to}-activity_id={$activity->id}</span>";
                    }
                }

                $nestedData['AssignedToName'] = $assigned_to;

                if ($activity->completion_datetime!=null) {
                    $checkedStatus = "no_uncheck";
                } else {
                    $checkedStatus = "no_check";
                }

                if (Auth::user()->can('activity-status') && (Auth::user()->isCompanyManager() || Auth::user()->EmployeeId()==$activity->created_by || Auth::user()->EmployeeId()==$activity->assigned_to)) {
                    $chckedSt = $activity->completion_datetime!=null?'checked':'';
                    $completion = "<div class='round'><input type='checkbox' id='act{$activity->id}' class='check check_{$activity->id}' name='status' value='{$activity->id}' {$chckedSt}><label for='act{$activity->id}'></label></div>";
                } else {
                    $completion = "<div class='round'><input type='checkbox' id='act{$activity->id}' readonly='readonly' class='{$checkedStatus}' name='status' value='{$activity->id}' {{ ($activity->completion_datetime!=NULL)?'checked':''}}><label for='act{{$activity->id}}'></label></div>";
                }

                $nestedData['completed'] = $completion;

                $action = "<a style='color:green;font-size: 15px;margin-left:5px;' href='{$show}'><i class='fa fa-eye'></i></a>";
                
                if (Auth::user()->can('activity-update')) {
                    if (Auth::user()->isCompanyManager() || Auth::user()->EmployeeId()== $activity->created_by || Auth::user()->EmployeeId() ==$activity->assigned_to) {
                        $action = $action ."<a style='color:#f0ad4e!important;font-size: 15px;margin-left:5px;' href='{$edit}'><i class='fa fa-edit'></i></a>";
                    }
                }

                if ((Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) || $activity->created_by==Auth::user()->EmployeeID() && Auth::user()->can('activity-delete')) {
                    $action = $action."<a style='color:red;font-size: 15px;margin-left:5px;' data-mid='{$activity->id}' data-url='{$delete}' data-toggle='modal' data-target='#delete'><i class='fa fa-trash-o'></i></a>";
                }

                $nestedData['action'] = $action;
                
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

    public function employeeZeroOrdersTable(Request $request){
        $getColumns = $request->columns;
        $columns = array();
        $sizeof = sizeof($getColumns);
        for($count = 0; $count<$sizeof; $count++){
          $columns[$count] = $getColumns[$count]["data"];
        }

        $units = $request->units;

        $company_id = config('settings.company_id');
        $emplVal = $request->get('employeeID');
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order_col_no = $request->input('order.0.column');
        $order = $columns[$order_col_no];
        if($order == "id"){
          $order = "no_orders."."id";
        }elseif($order == "client_name"){
          $order = "clients."."company_name";
        }elseif($order == "date"){
          $order = "no_orders.id";//.$order;
        }elseif($order == "date"){
          $order = "no_orders.remark";
        }
        $dir = $request->input('order.0.dir');

        $prepQuery = NoOrder::select('clients.id as client_id', 'clients.name as contact_person_name', 'clients.company_name', 'clients.mobile as contact_number', 'clients.superior as party_superior', 'clients.client_type as party_type', 'partytypes.name as party_type_name','clients.address_1 as address', 'employees.name as employee_name', 'no_orders.employee_id as employee_id','no_orders.id as noorder_id', 'no_orders.company_id', 'no_orders.date', 'no_orders.remark')
                  ->leftJoin('clients', 'no_orders.client_id', '=', 'clients.id')
                  ->leftJoin('employees', 'no_orders.employee_id', '=', 'employees.id')
                  ->leftJoin('partytypes', 'clients.client_type', '=', 'partytypes.id')
                  ->leftJoin('client_settings', 'no_orders.company_id','client_settings.company_id')->where('no_orders.company_id',$company_id)->where('no_orders.employee_id', $emplVal);

        if(!empty($search)){
          $searchQuery = (clone $prepQuery);
          $prepQuery = $searchQuery->where(function($query) use ($search){
                        $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                        $query->orWhere('clients.mobile' ,'LIKE', "%{$search}%");
                        $query->orWhere('partytypes.name' ,'LIKE', "%{$search}%");
                        $query->orWhere('no_orders.remark' ,'LIKE', "%{$search}%");
                        });
        }

        $totalData = $prepQuery->count();
        if($limit==-1){
          $limit = $totalData;
        }
        $totalFiltered = $totalData;

        $data = array();

        $no_orders =  $prepQuery->orderBy($order,$dir)->offset($start)
                              ->limit($limit)->get();

        if (!empty($no_orders)) {
            $i = $start;
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            foreach ($no_orders as $noorder) {
              $id = $noorder->noorder_id;

              $show = domain_route('company.admin.zeroorder.show', [$id]);
              $edit = domain_route('company.admin.zeroorder.edit', [$id]);
              $delete = domain_route('company.admin.zeroorder.destroy', [$id]);


              $client_name = $noorder->company_name;
              $client_show = in_array($noorder->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$noorder->client_id]):null;
              $date = isset($noorder->date)?getDeltaDate(date('Y-m-d',strtotime($noorder->date))):null;
              $remark = $noorder->remark;
              
              $nestedData['id'] = ++$i;
              $nestedData['client_name'] = "<a href='{$client_show}' datasalesman='{$client_name}' data-viewable='{$client_show}' class='clientLinks'> {$client_name}</a>";
              $nestedData['date'] =$date;
              $nestedData['remark'] = $remark;

              $action = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

              if(Auth::user()->can('zeroorder-update')){
                $action = $action."<a href='{$edit}' class='btn btn-warning btn-sm'
                style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
              }
              if(Auth::user()->can('zeroorder-delete')){
                $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
              }
              $nestedData['action'] = $action;

              
              // $client_company_name = $noorder->company_name;
              // $client_show = domain_route('company.admin.client.show',[$noorder->client_id]);
              // $client_name = $noorder->contact_person_name;
              // $party_type =  $noorder->party_type_name;
              // $contact =  $noorder->contact_number;
              // $address =  $noorder->address;
              // $nestedData['contact_person'] = $client_name;
              // $nestedData['party_type'] = $party_type;
              // $nestedData['contact_number'] = $contact;
              // $nestedData['address'] = $address;

              $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        );

        return json_encode($json_data);
    }


    public function empClientVisitTable(Request $request)
    {
        $columns = array( 'id', 'date', 'no_of_visits', 'view_detail' );

        $company_id = config('settings.company_id');
        $empVal = $request->empID;
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        if($order == 'no_of_visits'){
          $order = \DB::raw('COUNT(*)');
        }
        $dir = $request->input('order.0.dir');

        $prepQuery = ClientVisit::whereCompanyId($company_id)
                      ->whereEmployeeId($empVal)
                      ->groupBy('date');

        if (!empty($search)) {
            $searchQuery = $prepQuery;
            $prepQuery = $searchQuery->where(function ($query) use ($search) {
                $query->orWhere(\DB::raw('COUNT(*)'), 'LIKE', "%{$search}%");
            });
        }

        // $totalData =  $prepQuery->count();
        $totalData =  (clone $prepQuery)->get()->count();
        if($limit==-1) $limit = $totalData;
        $totalFiltered = $totalData;
      
        $data = array();
        $clientvisits = $prepQuery->orderBy($order, $dir)->offset($start)
                            ->limit($limit)
                            ->get(['date', \DB::raw('COUNT(*) as no_of_visits')]);
        
        if (!empty($clientvisits)) {
            $i = $start;
            foreach ($clientvisits as $clientvisit) {
                // $id = $clientvisit->id;
                $clientvisit_date = $clientvisit->date;
                $no_of_visits = $clientvisit->no_of_visits;
                $detail = domain_route('company.admin.employee.empClientVisitDetail', ['id' => $empVal, 'date' => $clientvisit_date]);
                $nestedData['id'] = ++$i;
                $nestedData['date'] = getDeltaDate($clientvisit_date);
                $nestedData['no_of_visits'] = $no_of_visits;

                $nestedData['view_detail'] = "<a href='{$detail}' class='btn btn-success btn-sm' style='color: #05c16b!important;padding: 3px 6px;border: none;background-color: #05c16b00!important;'><i class='fa fa-eye'></i></a>";
            
                $data[] = $nestedData;
            }
        }

        $json_data = array(
        "draw"            => intval($request->input('draw')),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data,
      );

      return json_encode($json_data);
    }

    public function empClientVisitDetail(Request $request)
    {
      $data = $this->getVisitData($request);

      return view('company.employees.visit_details')->with($data);
    }

    private function getVisitData($request){
      $company_id = $this->company_id;
      $empId = $request->id;
      $date = $request->date;
      $hr = 0;
      $min = 0;
      
      $empVisits = ClientVisit::whereCompanyId($company_id)->whereEmployeeId($empId)->where('date', '=',$date)->with(['employee' => function($query){
                      return $query->select('employees.name', 'employees.id');
                    }])->with(['client' => function($query){
                      return $query->select('clients.company_name', 'clients.id');
                    }])->with(['visitpurpose' => function($query){
                      return $query->select('visit_purposes.title', 'visit_purposes.id');
                    }])->with('images')->orderby('id', 'desc')->get()->map(function($client_visit_purpose) use($hr, $min){
                      $formatted_data = $this->formatClientVisit($client_visit_purpose); 
                      return $formatted_data;
                  });
      $total_duration = "";
      foreach($empVisits as $formatted_data){
        $hr += $formatted_data['duration']['hr'];
        $min += $formatted_data['duration']['min'];
        $total_duration = $this->getTotalDuration($hr, $min);
      }
                      
      $attendance_details = Attendance::whereEmployeeId($empId)->where('adate', '=',$date);
      $checkin = (clone $attendance_details)->where('check_type', 1)->first([\DB::raw('MIN(atime) as checkIn')])->checkIn;
      $checkout = (clone $attendance_details)->where('check_type', 2)->first([\DB::raw('MAX(atime) as checkOut')])->checkOut;
      $employee_name = $empVisits->first()['employee_name'];
      $action = NULL;
      $download = domain_route('company.admin.employee.empClientVisitDetailDownload', ['id' => $empId, 'date' => $date]);
      
      $action  = '<a class="btn btn-default btn-sm" href="'.$download.'" style="padding: 7px 6px;margin-right: 5px;"><i class="fa fa-book"></i>PDF</a>';
          
      $action  = $action.'<button class="btn btn-default btn-sm" href="#" onclick="print();" style="padding: 7px 6px;"><i class="fa fa-print"></i>Print</button>';

      $data = [
        'action' => $action,
        'checkin' => $checkin, 
        'checkout' => $checkout, 
        'date' => $date, 
        'employee_id' => $empId,
        'employee_name' => $employee_name, 
        'empVisits' => $empVisits, 
        'total_duration' => $total_duration, 
      ];

      return $data;
    }

    public function empClientVisitDetailDownload(Request $request){
      $data = $this->getVisitData($request);

      try{
        $pdf = PDF::loadView('company.employees.partial_show.visit_detail_download', $data);

        return $pdf->download($data['employee_name'].'_'.$data['date'].'_'.'_Visit_Report.pdf');
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());

        return redirect()->back();
      }
    }

    public function empClientVisitDetailPrint(Request $request){
      $data = $this->getVisitData($request);

      try{
        return view('company.employees.partial_show.visit_detail_download', $data);
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());

        return redirect()->back();
      }
    }

    private function formatClientVisit($object){
      try{
        $start_time = new DateTime($object->start_time);
        $end_time = new DateTime($object->end_time);
        $interval = $end_time->diff($start_time);
        $hr = $interval->h;
        $min = $interval->i;
        $total_duration = $this->getTotalDuration($hr, $min);
        
        $formatted_data = [
          'id' => $object->id,
          'client_id' => $object->client_id,
          'client_name' => $object->client->company_name,
          'employee_id' => $object->employee_id,
          'employee_name' => $object->employee->name,
          'visit_purpose_id' => $object->visitpurpose->id,
          'visit_purpose' => $object->visitpurpose->title,
          "date" => $object->date,
          "start_time" => date("H:i A", strtotime($object->start_time)),
          "end_time" => date("H:i A", strtotime($object->end_time)),
          "duration" => array("hr" => $hr, "min" => $min),
          "total_duration" => $total_duration?$total_duration : 0 . " second", 
          "comments" => $object->comments,
          'images' => $object->images->map(function($image) {
                        return $this->formatImages($image);
                      }),
        ];
        return $formatted_data;
      }catch(\Exception $e){
        Log::error(array("Format Client Visit Purpose () => "), array($e->getMessage()));
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        
        return array();
      }
    }

    private function formatImages($image){
      try{
        $formatted_data = [
          'id' => $image->id,
          'image_name' => $image->image, 
          'image_path' => $image->image_path
        ];

        return $formatted_data;        
      }catch(\Exception $e){
        Log::error(array("Format Images () => "), array($e->getMessage()));
        Log::info($images);
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        return null;
      }
    }

    private function getTotalDuration($hr, $min){
      try{
        if($min>60) {
          $minToHr = $min/60;
          $hr += $minToHr;
          $min = $min%60;
        }
        $hr_text = $hr > 1 ? " Hours" : " Hour";
        $min_text = $min > 1 ? " Minutes" : " Minute";
        $hr_duration = $hr > 0 ? $hr . $hr_text : "";
        $min_duration = $min > 0 ? $min . $min_text : "";
        $total_duration = ($hr_duration?$hr_duration." ":$hr_duration).$min_duration;

        return $total_duration;
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        
        return "";
      }
    }

}