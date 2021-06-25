<?php

namespace App\Http\Controllers\Admin;

//use DB;
use Mail;
use View;
use Excel;
use Storage;
use App\Plan;
use App\User;
use DateTime;
use App\Client;
use App\Company;
use App\Manager;
use App\TaxType;
use App\Employee;
use DateTimeZone;
use App\LeaveType;
use App\PartyType;
use App\MainModule;
use App\MarketArea;
use App\Designation;
use App\ExpenseType;
use App\LogActivity;
use App\PartyUpload;
use App\ActivityType;
use App\CustomModule;

use App\ReturnReason;
use App\VisitPurpose;
use App\ClientSetting;


//use App\Plan;
//use App\ReturnReason;
//use App\TaxType;
//use App\User;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use App\ModuleAttribute;


use App\ActivityPriority;
use App\PartyUploadFolder;
use App\PermissionCategory;
use Illuminate\Http\Request;
use App\Exports\EmployeeExports;
use App\Jobs\DefaultLevelValues;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Jobs\AssignFullAccessPermission;
use Spatie\Permission\Models\Permission;
use App\Jobs\AssignLimitedAccessPermission;
use Symfony\Component\HttpFoundation\Session\Session;

class CompanyController extends Controller
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

    public function index(Request $request)
    {
        // $this->updateOldRecords();
        // $this->updateOutletSettings();
        $companies_count = Company::count();
        $request->session()->put('active', 'profile');
        return view('admin.companies.index', compact('companies_count'));
    }

    // private function updateOldRecords(){
    //   $available_plans = Plan::whereHas('companies')->get();
    //   foreach($available_plans as $available_plan){
    //     Company::whereIn('id', $available_plan->companies()->pluck('companies.id')->toArray())->update([
    //       "num_users" => $available_plan->users,
    //     ]);
    //   }
    // }

    // private function updateOutletSettings(){
    //   $companies = Company::all();
    //   foreach($companies as $company){
    //     if($company->clientsettings){
    //       if(! $company->clientsettings->client_outlet_settings){
    //         DB::table('client_outlet_settings')->insert([
    //           'company_id'=> $company->id,
    //           'min_order_value'=> 0.00
    //         ]);
    //       }
    //     }
    //   }
    // }

    public function dataTable(Request $request)
    {
        $columns = array('id', 'company_name', 'domain', 'contact_name', 'contact_phone', 'contact_email', 'end_date', 'company_plan', 'num_users', 'is_active', 'customer_status', 'action');

        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $search = $request->input('search.value');
        $filterDays = $request->get('filterDays');
        $accountStatusFilter = $request->get('account_status');
        $customerStatusFilter = $request->get('customer_status');


        $totalData = Company::get()->count();
        $getCompanies = Company::select('companies.id', 'companies.company_name', 'companies.domain', 'companies.contact_phone', 'companies.contact_email', DB::raw('CAST(companies.start_date AS DATE) as start_date'), DB::raw('CAST(companies.end_date AS DATE) as end_date'), 'companies.num_users', 'companies.is_active', 'companies.customer_status', 'companies.note', 'managers.contact_name', 'plans.name as company_plan', 'company_active_staus.status_id', 'company_active_staus.name as status_name')
            ->leftJoin('managers', 'managers.company_id', 'companies.id')
            ->leftJoin('company_plan', 'companies.id', 'company_plan.company_id')
            ->leftJoin('company_active_staus', 'company_active_staus.status_id', 'companies.is_active')
            ->leftJoin('plans', 'company_plan.plan_id', 'plans.id');
        if (isset($accountStatusFilter)) {
            if ($accountStatusFilter != 4 && $accountStatusFilter != 2) $getCompanies = $getCompanies->where('companies.is_active', $accountStatusFilter);
            elseif ($accountStatusFilter != 4 && $accountStatusFilter == 2) $getCompanies = $getCompanies->where('companies.is_active', $accountStatusFilter)->where(DB::raw('CAST(companies.end_date AS DATE)'), '>=', date('Y-m-d'));
            elseif ($accountStatusFilter == 4) $getCompanies = $getCompanies->where('companies.is_active', 2)->where(DB::raw('CAST(companies.end_date AS DATE)'), '<', date('Y-m-d'));
        }
        if (isset($customerStatusFilter)) {
            $getCompanies = $getCompanies->where('companies.customer_status', $customerStatusFilter);
        }
        if (isset($search)) {
            $getCompanies = $getCompanies->where(function ($query) use ($search) {
                $query->orWhere('companies.company_name', 'LIKE', "%{$search}%");
                $query->orWhere('companies.domain', 'LIKE', "%{$search}%");
                $query->orWhere('companies.contact_phone', 'LIKE', "%{$search}%");
                $query->orWhere('companies.contact_email', 'LIKE', "%{$search}%");
                $query->orWhere('companies.num_users', 'LIKE', "%{$search}%");
                $query->orWhere('companies.customer_status', 'LIKE', "%{$search}%");
                $query->orWhere('company_active_staus.name', 'LIKE', "%{$search}%");
                $query->orWhere('plans.name', 'LIKE', "%{$search}%");
                $query->orWhere('managers.contact_name', 'LIKE', "%{$search}%");
            });
        }
        if ($filterDays) {
            $today = date("Y-m-d");

            if ($filterDays == "45") $daysWithin = date("Y-m-d", strtotime($today . '+45 days'));
            elseif ($filterDays == "30") $daysWithin = date("Y-m-d", strtotime($today . '+30 days'));
            $getCompanies = $getCompanies->whereBetween('companies.end_date', [$today, $daysWithin]);
        }

        $totalFiltered = (clone $getCompanies)->count();
        $companies = $getCompanies->orderBy($order, $dir)->offset($start)
            ->limit($limit)->get();
        $data = array();
        if (!empty($companies)) {
            $i = $start;
            foreach ($companies as $company) {
                $id = $company->id;

                $note = domain_route('app.company.notes', [$id]);
                $setting = domain_route('app.company.setting', [$id]);
                $edit = domain_route('app.company.edit', [$id]);
                $download = domain_route('app.company.empdownload', [$id]);
                $status_chane_action = domain_route('app.company.updatestatus', [$id]);

                $note_action = "<a href='#' class='btn btn-warning btn-xs btnsFlex' id='add-note' data-note='{$company->note}' data-href='{$note}'><i class='glyphicon glyphicon-book'></i></a>";
                $setting_action = "<a href='{$setting}' class='btn btn-warning btn-xs btnsFlex'><i class='fa fa-cog'></i></a>";
                $edit_action = "<a href='{$edit}' class='btn btn-warning btn-xs btnsFlex'><i class='fa fa-edit'></i></a>";
                $download_action = "<a href='{$download}' class='btn btn-primary btn-xs btnsFlex'><i class='fa fa-download'></i></a>";

                $company_name = $company->company_name;
                $domain = $company->domain;
                $contact_name = $company->contact_name;
                $contact_phone = $company->contact_phone;
                $contact_email = $company->contact_email;
                $end_date = date('d M Y', strtotime($company->end_date));
                $plan = $company->company_plan;
                $num_users = $company->num_users;
                $status_name = $company->status_name;
                if (date('Y-m-d') > $company->end_date && $company->is_active == '2') {
                    $statusLabel = '<span class="label label-warning" data-attr="Extension">Extension</span>';
                } else {
                    if ($company->is_active == '2') {
                        $statusLabel = '<span class="label label-success" data-attr="Active">Active</span>';
                    } elseif ($company->is_active == '1') {
                        $statusLabel = '<span class="label label-primary" data-attr="Disabled">Disabled</span>';
                    } else {
                        $statusLabel = '<span class="label label-danger" data-attr="Expired">Expired</span>';
                    }
                }
                $account_status = $company->is_active;
                $customer_status = $company->customer_status;
                if ($customer_status == "trial") {
                    $customerStsLabel = '<span class="label label-primary" data-attr="Trial">Trial</span>';
                } else {
                    $customerStsLabel = '<span class="label label-success" data-attr="Customer">Customer</span>';
                }

                $action = "<div class='flexBtnDiv'>" . $note_action . $setting_action . $edit_action . $download_action . "</div>";

                $nestedData['id'] = ++$i;
                $nestedData['company_name'] = $company_name;
                $nestedData['domain'] = $domain;
                $nestedData['contact_name'] = $contact_name;
                $nestedData['contact_phone'] = $contact_phone;
                $nestedData['contact_email'] = $contact_email;
                $nestedData['end_date'] = $end_date;
                $nestedData['company_plan'] = $plan;
                $nestedData['num_users'] = $num_users;
                $nestedData['is_active'] = "<a href='#' class='update-status-modal' data-value='{$company->is_active}' data-company_id='{$id}' data-status-type='is_active' data-action='{$status_chane_action}'>{$statusLabel}</a>";
                $nestedData['customer_status'] = "<a href='#' class='update-status-modal' data-value='{$customer_status}' data-company_id='{$id}' data-status-type='customer_status' data-action='{$status_chane_action}'>{$customerStsLabel}</a>";
                $nestedData['action'] = $action;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
            "total" => null
        );

        return json_encode($json_data);
    }

    public function generateAndMailOTP(Request $request)
    {

        $company_id = $request->company_id;
        try {

            $company_name = Company::find($company_id)->company_name;
            $otp = rand(1000, 10000);
            $save_otp = ClientSetting::where('company_id', $company_id)->update([
                'otp' => $otp
            ]);
            $emails = env('SUPER_ADMIN_OTP_MAIL') ? env('SUPER_ADMIN_OTP_MAIL') : "deltatechektagolchha@gmail.com";
            $ccemails = env('SUPER_ADMIN_OTP_MAIL_CC') ? explode(",", env('SUPER_ADMIN_OTP_MAIL_CC')) : array();
            $subject = "OTP For Changing Company Status";
            $html = "Company Name :- " . $company_name . "\n" . "OTP :- " . $otp;
            Mail::raw($html, function ($message) use ($emails, $ccemails, $subject) {
                $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
                $message->to($emails)->cc($ccemails);
                $message->subject($subject);
            });
            return response()->json([
                'status' => 200,
                'msg' => 'Generated. Please check mail for OTP.'
            ]);
        } catch (Exception $e) {
            Log::info($e->getCode());
            Log::info($e->getMsg());
            return response()->json([
                'status' => 400,
                'msg' => 'Failed.'
            ]);
        }

    }

    public function updateStatus(Request $request)
    {
        $company_id = $request->id;
        $field_type = $request->field_type;
        $company_instance = Company::findOrFail($company_id);
        $chosen_status = $request->chosen_status;
        if ($chosen_status) {
            $customMessages = [
                'otp.required' => 'Please enter OTP to proceed.'
            ];

            $this->validate($request, [
                'otp' => 'required'
            ], $customMessages);

            if ($company_instance->clientsettings->otp != $request->otp) return response()->json([
                'status' => 422,
                'msg' => 'OTP Mismatch.'
            ]);
        }
        $company_instance->$field_type = $chosen_status;

        $updated = $company_instance->update();
        ClientSetting::where('company_id', $company_id)->update(['otp' => NULL]);
        if ($updated) {
            if ($chosen_status == "0" || $chosen_status == "1") {
                $logout_fbIDs = DB::table('employees')->where(array(array('company_id', $company_id)))->whereNotNull('firebase_token')->pluck('firebase_token');

                DB::beginTransaction();
                $updateLogoutFlag = User::where('company_id', $company_id)->update([
                    'is_logged_in' => 0
                ]);
                DB::commit();
                $logout_dataPayload = array("data_type" => "employee", "employee" => null, "action" => "logout");
                $logout_sent = sendPushNotification_($logout_fbIDs, 31, null, $logout_dataPayload);
            } else {
                $manager = Manager::where('company_id', $request->id)->first();
                $admin = Employee::where('user_id', $manager->user_id)->first();
                $stat = 'Active';
                $admin->status = 'Active';
                $admin->save();
            }

            return response()->json([
                'status' => 200,
                'msg' => 'Updated.'
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'msg' => 'Failed.'
            ]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plans = Plan::where('custom', 0)->where('status', 'Active')->get();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            // date_default_timezone_set($zone);
            $timezonelist[$zone] = '(UTC/GMT ' . date('P', $timestamp) . ') ' . $zone;
        }
        return view('admin.companies.create', compact('plans', 'timezonelist'));
    }

    /**
     * Store a newly created resource in storage.ssss
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    private function createDNS($subdomain)
    {
        /* Cloudflare.com | APİv4 | Api Ayarları */
        $apikey = '79d9caf3246e5f787b3b7b5da77d179c06c50'; // Cloudflare Global API
        $email = 'deltatechektagolchha@gmail.com'; // Cloudflare Email Adress
        $domain = 'deltasalesapp.com';  // zone_name // Cloudflare Domain Name
        $zoneid = '9a0cefc6fe6041fe08cd5956bca1096e'; // zone_id // Cloudflare Domain Zone ID


        // A-record oluşturur DNS sistemi için.
        $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zoneid . "/dns_records");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $email . '',
            'X-Auth-Key: ' . $apikey . '',
            'Cache-Control: no-cache',
            // 'Content-Type: multipart/form-data; charset=utf-8',
            'Content-Type:application/json',
            'purge_everything: true'

        ));

        // -d curl parametresi.
        $data = array(
            'type' => 'A',
            'name' => $subdomain . '.' . $domain,
            'content' => '139.59.71.228',
            'zone_name' => $domain,
            'zone_id' => $zoneid,
            'proxied' => true,
            'ttl' => 120,
            'priority' => 10
        );


        $data_string = json_encode($data);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_string));

        $sonuc = curl_exec($ch);

        // If you want show output remove code slash.
        //print_r($sonuc);

        curl_close($ch);
    }

    // public function createVisitPurpose(){
    //   $companies = Company::all();
    //   foreach($companies as $company){
    //     \App\VisitPurpose::insert([
    //       [
    //           'title'=>'Taking Order',
    //           'company_id' => $company->id,

    //       ],
    //       [
    //           'title'=>'Checking Stock',
    //           'company_id' => $company->id,

    //       ],
    //       [
    //           'title'=>'Payment Collection',
    //           'company_id' => $company->id,

    //       ],
    //     ]);
    //   }
    // }

    private function createGlobalCompanyProperties($company_id)
    {
        ModuleAttribute::insert(array(
            array('company_id' => $company_id, 'title' => 'Approved', 'default' => '1', 'module_id' => 1, 'order_amt_flag' => 1, 'order_edit_flag' => 0, 'order_delete_flag' => 0, 'color' => '#3c8dbc'),
            array('company_id' => $company_id, 'title' => 'Pending', 'default' => '1', 'module_id' => 1, 'order_amt_flag' => 0, 'order_edit_flag' => 1, 'order_delete_flag' => 1, 'color' => '#0a0000'),
        ));
        DB::table('client_outlet_settings')->insert([
            'company_id' => $company_id,
            'min_order_value' => 0.00
        ]);
        $timestamp = date("Y-m-d h:i:s");
        ReturnReason::insert(array(
            array('company_id' => $company_id, 'name' => 'Expired', 'created_at' => $timestamp),
            array('company_id' => $company_id, 'name' => 'Damaged', 'created_at' => $timestamp),
        ));
        LeaveType::insert([
            [
                'name' => 'Sick Leave',
                'company_id' => $company_id,

            ],
            [
                'name' => 'Maternity Leave',
                'company_id' => $company_id,

            ],
            [
                'name' => 'Family Responsibility Leave',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Study Leave',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Leave for religious holidays',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Annual Leave',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Compassionate Leave',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Paternity Leave',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Leave in Lieu',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Miscellaneous/Others',
                'company_id' => $company_id,
            ],

        ]);

        ActivityType::insert([
            [
                'name' => 'Call',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Follow Up',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Meeting',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Visit',
                'company_id' => $company_id,
            ],
        ]);

        ActivityPriority::insert([
            [
                'name' => 'Low',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Medium',
                'company_id' => $company_id,
            ],
            [
                'name' => 'High',
                'company_id' => $company_id,
            ],
        ]);

        ExpenseType::insert([
            [
                'expensetype_name' => 'Travel',
                'company_id' => $company_id,
            ],
            [
                'expensetype_name' => 'Phone',
                'company_id' => $company_id,
            ],
            [
                'expensetype_name' => 'Fuel',
                'company_id' => $company_id,
            ],
            [
                'expensetype_name' => 'Other',
                'company_id' => $company_id,
            ],
        ]);

        VisitPurpose::insert([
            [
                'title' => 'Taking Order',
                'company_id' => $company_id,

            ],
            [
                'title' => 'Checking Stock',
                'company_id' => $company_id,

            ],
            [
                'title' => 'Payment Collection',
                'company_id' => $company_id,

            ],
        ]);
    }

    public function store(Request $request)
    {

        $customMessages = [
            'company_name.required' => 'The Name field is required.',
            'contact_name.required' => 'The Name field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
            'domain.regex' => 'Domain name can only contain alphabet.',
            'num_users' => 'No. of Users is required.',
            'num_users.numeric' => 'Users field should be in number.',
        ];
        $this->validate($request, [
            'company_name' => 'required',
            'time_zone' => 'required',
            'domain' => 'required|unique:companies|regex:/^[a-zA-Z]*$/',
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|digits_between:7,14',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
            'num_users' => 'required|numeric',
        ], $customMessages);
        DB::beginTransaction();
        $company = new \App\Company;
        $company->company_name = $request->get('company_name');
        $company->domain = $request->get('domain');
        $company->contact_email = $request->get('contact_email');
        $company->contact_name = $request->get('contact_name');
        $company->contact_phone = $request->get('contact_phone');
        $company->pan = $request->get('pan');
        $company->start_date = $request->get('start_date');
        $company->end_date = $request->get('end_date');
        $company->extNo = $request->get('extNo');
        $company->num_users = $request->get('num_users');
        $company->aboutCompany = $request->get('aboutCompany');
        $company->is_active = $request->get('is_active');
        $company->customer_status = $request->get('customer_status');
        $company->is_verified = 0;
        $company->verify_token = time();
        // $company->fax = $request->get('fax');
        // $company->is_live = $request->get('is_live');
        // $company->whitelabel = $request->get('whitelabel');
        // $company->customize = $request->get('customize');
        $save_company = $company->save();
        if ($save_company) {
            if(config('app.env') == "production") $this->createDNS($company->domain);
            $this->createGlobalCompanyProperties($company->id);
        }
        $user = new User;
        $user->name = $request->get('company_name');
        $user->email = $request->get('contact_email');
        $user->phone = $request->get('contact_phone');
        $user->password = bcrypt($request->get('password'));
        $user->is_active = $request->get('is_active');
        $user->company_id = $company->id;
        $save_user = $user->save();

        $designation = new Designation;
        $designation->name = 'Admin';
        $designation->parent_id = 0;
        $designation->company_id = $company->id;
        $designation->save();

        $designationSalesman = new Designation;
        $designationSalesman->name = 'Salesman';
        $designationSalesman->parent_id = $designation->id;
        $designationSalesman->company_id = $company->id;
        $designationSalesman->save();

        // $adminPermissions = ["21","22","23","24","25","41","42","43","44","45", "51", "52", "53", "54", "55", "36", "37", "38", "39", "40", "16", "17", "18", "19", "66", "67", "68", "69", "1", "2", "3", "4", "5", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "77", "47", "48", "50", "6", "7", "11", "12", "13", "14", "56", "57", "58", "59", "60", "81", "82", "88", "91", "92", "96", "97", "102", "107", "112", "172", "117", "127", "132", "137", "142", "147", "152", "157", "162", "167", "177", "182", "122", "172", "187", "83", "84", "111", "113", "114"
        // ];

        // $salesmanPermission = ["22","41","42","43","36","37","16","17","66","67", "1", "2", "3", "4", "26", "27", "31", "32", "33", "34", "47", "48", "50", "11", "12", "13", "56", "57", "81", "82", "91", "92", "96", "97", "102", "107", "112", "111"
        // ];

        $role = new Role;
        $role->name = 'Full Access';
        $role->company_id = $company->id;
        $role->guard_name = 'web';
        $role->save();
        // $role->syncPermissions($adminPermissions);
        $user->assignRole($role->id);

        // if ($role->id) {
        //     $role_id = $role->id;
        //     $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
        //     $permission_category = DB::table('permission_categories')->where('name', 'LIKE', '%Parties_Rate_Setup%')->first();
        //     if ($permission_category) {
        //         $permission_category_id = $permission_category->id;
        //         $permissions = DB::table('permissions')->wherePermissionCategoryId($permission_category_id)->pluck('name')->toArray();
        //         foreach ($permissions as $permission_name) {
        //             $role->givePermissionTo($permission_name);
        //         }
        //     }

        //     $permission_category_visit_module = DB::table('permission_categories')->where('name', 'LIKE', '%Party_Visit%')->first();
        //     if ($permission_category_visit_module) {
        //         $permission_category_visit_module_id = $permission_category_visit_module->id;
        //         $permissions_visit_module = DB::table('permissions')->wherePermissionCategoryId($permission_category_visit_module_id)->pluck('name')->toArray();
        //         foreach ($permissions_visit_module as $permission_name) {
        //             $role->givePermissionTo($permission_name);
        //         }
        //     }

        //     $role->givePermissionTo('Custom-Module-view');

        //     $role->givePermissionTo('imageuploads-create');
        //     $role->givePermissionTo('imageuploads-view');
        //     $role->givePermissionTo('imageuploads-update');
        //     $role->givePermissionTo('imageuploads-delete');
        //     $role->givePermissionTo('imageuploads-status');

        //     $role->givePermissionTo('fileuploads-create');
        //     $role->givePermissionTo('fileuploads-view');
        //     $role->givePermissionTo('fileuploads-update');
        //     $role->givePermissionTo('fileuploads-delete');
        //     $role->givePermissionTo('fileuploads-status');

        //     $role->givePermissionTo('import-create');
        //     $role->givePermissionTo('import-view');
        //     $role->givePermissionTo('import-update');
        //     $role->givePermissionTo('import-delete');
        //     $role->givePermissionTo('import-status');

        // }

        $salesmanRole = new Role;
        $salesmanRole->name = 'Limited Access';
        $salesmanRole->company_id = $company->id;
        $salesmanRole->guard_name = 'web';
        $salesmanRole->save();
        // $salesmanRole->syncPermissions($salesmanPermission);
        // $limited_access_assign_permission_name = array('PartyVisit-view', 'PartyVisit-create');
        // AssignLimitedAccessPermission::dispatch($company->id, $limited_access_assign_permission_name);

        
        
        $owner = new Manager;
        $owner->contact_name = $request->get('contact_name');
        $owner->is_owner = 1;
        $owner->assignUser($user);

        $eowner = new Employee;
        $eowner->name = $request->get('contact_name');
        $eowner->is_admin = 1;
        $eowner->is_owner = 1;
        $eowner->email = $request->get('contact_email');
        $eowner->phone = $request->get('contact_phone');
        $eowner->designation = $designation->id;
        $eowner->password = $request->get('password');
        $eowner->role = $role->id;
        $eowner->status = $request->get('is_active') == 2 ? 'Active' : 'Inactive';
        $eowner->company_id = $company->id;
        $eowner->assignUser($user);
        $save_eowner = $eowner->save();
        $save_owner = $company->owners()->save($owner);

        $plan = Plan::where('id', $request->get('plan'))->first();
        $company->plans()->attach($plan);

        $modules = MainModule::leftJoin('plan_has_modules', 'main_modules.id', 'plan_has_modules.module_id')
            ->select('main_modules.*', 'plan_has_modules.id as plan_module_id', 'plan_has_modules.plan_id', 'plan_has_modules.module_id', 'plan_has_modules.enabled')
            ->where('plan_has_modules.plan_id', $plan->id)->get();
        $siteinfo = new ClientSetting;
        $siteinfo->company_id = $company->id;
        $siteinfo->title = $request->get('company_name');
        $siteinfo->email = $request->get('contact_email');
        $siteinfo->phone = $request->get('contact_phone');
        $siteinfo->mobile = $request->get('contact_phone');
        $siteinfo->ext_no = $request->get('extNo');
        $siteinfo->date_format = config('app.date_format');
        $siteinfo->time_zone = $request->get('time_zone');
        foreach ($modules as $module) {
            $field = $module->field;
            if ($module->enabled == 1) {
                $siteinfo->$field = 1;
                // if ($field == 'retailer_app') {
                //     $role = Role::whereCompanyId($company->id)->whereName('Full Access')->whereGuardName('web')->first();
                //     if ($role) {
                //         $role_id = $role->id;
                //         $permission_category = DB::table('permission_categories')->whereName('outlet')->whereDisplayName('Outlet Connection')->first();
                //         if ($permission_category) {
                //             $permission_category_id = $permission_category->id;
                //             $permissions = DB::table('permissions')->wherePermissionCategoryId($permission_category_id)->pluck('id')->toArray();
                //             if (!empty($permissions)) {
                //                 $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                //                 $non_exist_role_permission = array_merge($permissions, $exist_role_permissions);
                //                 if (!empty($non_exist_role_permission)) {
                //                     $role->syncPermissions($non_exist_role_permission);
                //                 }
                //             }
                //         }
                //     }

                // }
            } else {
                $siteinfo->$field = 0;
            }
        }
        $save_setting = $siteinfo->save();

        $affected = DB::table('tally_schedule')->insert([
            'company_id' => $company->id,
            'company_name' => $company->domain,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        

        DB::commit();
        AssignFullAccessPermission::dispatch($company->id, null);
        if ($save_company && $save_user && $save_owner && $save_setting) {
            $emails = $user->email;
            $subject = 'Email Verification';
            $url = 'https://' . config('app.domain') . '/verify/' . $company->id . '/' . $company->verify_token;
            try{
              Mail::send('company.verify', ['url' => $url, 'company_name' => $company->company_name], function ($message) use ($emails, $subject) {
                  $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
                  $message->to($emails);
                  $message->bcc('dev@deltatechnepal.com');
                  $message->subject($subject);
              });

            }catch(\Exception $e){
              Log::error(array( "Company Create Mail Send Domain : ", $company->domain, $e->getMessage() ));
              return redirect()->route('app.company')->with('success', 'Information has been  Added. Please verify the email address for company login');
            }
        }


        return redirect()->route('app.company')->with('success', 'Information has been  Added. Please verify the email address for company login');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('app.company');
        $company = Company::find($id);
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company = Company::select('companies.id', 'companies.company_name', 'companies.domain', 'companies.contact_phone', 'companies.contact_email', 'companies.start_date', 'companies.end_date', 'companies.is_active', 'plans.id as plan_id', 'plans.name as plan_name', 'companies.num_users', 'companies.customer_status')
            ->leftJoin('company_plan', 'companies.id', 'company_plan.company_id')
            ->leftJoin('plans', 'company_plan.plan_id', 'plans.id')
            ->orderBy('companies.created_at', 'DESC')
            ->where('companies.id', $request->id)
            ->first();
        $plans = Plan::where(function ($q) use ($company) {
            $q = $q->where('custom', 0)->orWhere('custom', $company->id);
        })->where('status', 'Active')->get();
        $manager = Manager::where('company_id', $request->id)->first();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            // date_default_timezone_set($zone);
            $timezonelist[$zone] = '(UTC/GMT ' . date('P', $timestamp) . ') ' . $zone;
        }
        $current_time_zone = ClientSetting::where('company_id', $request->id)->first()->time_zone;

        return view('admin.companies.edit', compact('company', 'plans', 'manager', 'timezonelist', 'current_time_zone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {


        $company = Company::findOrFail($request->id);
        $getCurrentCount = Employee::where('company_id', $request->id)->whereStatus("Active")->count();

        $customMessages = [
            'company_name.required' => 'The Name field is required.',
            'contact_name.required' => 'The Name field is required.',
            'num_users' => 'No. of Users is required.',
            'num_users.numeric' => 'Users field should be in number.',
            'num_users.min' => 'Number of Users must not be less than current active users.',
        ];


        $this->validate($request, [
            'company_name' => 'required',
            'time_zone' => 'required',
            'domain' => 'required|unique:companies,domain,' . $request->id,
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|digits_between:7,14',
            'num_users' => 'required|numeric|min:' . $getCurrentCount,
        ], $customMessages);


        $company->company_name = $request->get('company_name');
        $company->domain = $request->get('domain');
        $company->contact_email = $request->get('contact_email');
        $company->contact_phone = $request->get('contact_phone');
        $company->contact_name = $request->get('contact_name');
        // $company->fax = $request->get('fax');
        $company->pan = $request->get('pan');
        // $company->whitelabel = $request->get('whitelabel');
        // $company->customize = $request->get('customize');
        $company->start_date = $request->get('start_date');
        $company->end_date = $request->get('end_date');
        $company->extNo = $request->get('extNo');
        $company->aboutCompany = $request->get('aboutCompany');
        $company->is_active = $request->get('is_active');
        $company->customer_status = $request->get('customer_status');
        $company->num_users = $request->get('num_users');
        // $company->is_live = $request->get('is_live');

        $company->save();
        $moduleAttributesApprovedExists = ModuleAttribute::where('company_id', $company->id)->where('title', 'Approved')->count();
        if ($moduleAttributesApprovedExists == 0) {
            ModuleAttribute::insert(array(
                array('company_id' => $company->id, 'title' => 'Approved', 'default' => '1', 'module_id' => 1, 'color' => '#3c8dbc'),
            ));
        }
        $moduleAttributesPendingExists = ModuleAttribute::where('company_id', $company->id)->where('title', 'Pending')->count();
        if ($moduleAttributesPendingExists == 0) {
            ModuleAttribute::insert(array(
                array('company_id' => $company->id, 'title' => 'Pending', 'default' => '1', 'module_id' => 1, 'color' => '#0a0000'),
            ));
        }
        $manager = Manager::where('company_id', $request->id)->first();
        $manager->contact_name = $request->get('contact_name');
        $manager->contact_email = $request->get('contact_email');
        $manager->save();

        $desigadmin = Designation::where('name', 'Admin')->where('company_id', $company->id)->first();
        if (!$desigadmin) {
            $designation = new \App\Designation;
            $designation->name = 'Admin';
            $designation->parent_id = 0;
            $designation->company_id = $company->id;
            $designation->save();
        }

        $roleAdmin = Role::where('name', 'Full Access')->where('company_id', $company->id)->first();
        $user = User::where('id', $manager->user_id)->first();
        if (!$roleAdmin) {
            $role = new Role;
            $role->name = 'Full Access';
            $role->company_id = $company->id;
            $role->guard_name = 'web';
            $role->save();
            $user->assignRole($role->id);

            // $permission_category_visit_module = DB::table('permission_categories')->where('name', 'LIKE', '%Party_Visit%')->first();
            // if ($permission_category_visit_module) {
            //     $permission_category_visit_module_id = $permission_category_visit_module->id;
            //     $permissions_visit_module = DB::table('permissions')->wherePermissionCategoryId($permission_category_visit_module_id)->pluck('name')->toArray();
            //     foreach ($permissions_visit_module as $permission_name) {
            //         $role->givePermissionTo($permission_name);
            //     }
            // }
        }

        $admin = Employee::where('user_id', $manager->user_id)->first();
        if ($admin) {
            $admin->name = $request->get('contact_name');
            $admin->email = $request->get('contact_email');
            $admin->is_admin = 1;
            if (!$desigadmin) {
                $admin->designation = $designation->id;
            } else {
                $admin->designation = $desigadmin->id;
            }
            $admin->phone = $request->get('contact_phone');
            if ($request->password)
                $admin->password = $request->get('password');
            // if($request->get('is_active')==2)
            //   $stat='Active';
            // else
            //   $stat='Inactive'; 
            // $admin->status = $stat;
            $save_admin = $admin->save();

            $employee_id = $admin->id;
            $company_id = $request->id;

            DB::table('announce_employee')
                ->where('company_id', $company_id)
                ->where('employee_id', 0)
                ->update(['employee_id' => $employee_id]);

            DB::table('beatvplans')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('clients')
                ->where('company_id', $company_id)->where('created_by', 0)->update(['created_by' => $employee_id]);

            DB::table('collections')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('day_remarks')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('expenses')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('handles')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('images')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('leaves')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('meetings')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('notifications')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('no_orders')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('orders')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('returns')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('stocks')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('activities')
                ->where('company_id', $company_id)->where('assigned_to', 0)
                ->update(['assigned_to' => $employee_id]);

            DB::table('activities')
                ->where('company_id', $company_id)->where('created_by', 0)
                ->update(['created_by' => $employee_id]);

            DB::table('tasks')
                ->where('company_id', $company_id)->where('assigned_from', 0)
                ->update(['assigned_from' => $employee_id]);

            DB::table('tasks')
                ->where('company_id', $company_id)->where('assigned_to', 0)
                ->update(['assigned_to' => $employee_id]);

            $parties = DB::table('clients')
                ->where('company_id', $company_id)->get();
            // print_r($parties);
            // die;
            foreach ($parties as $party) {
                $handle = DB::table('handles')
                    ->where('company_id', $company_id)->where('employee_id', $employee_id)->where('map_type', 1)->first();
                if (!$handle) {
                    DB::table('handles')->insert([
                        'company_id' => $company_id,
                        'employee_id' => $employee_id,
                        'client_id' => $party->id,
                        'map_type' => 1
                    ]);
                }
            }


        } else {

            $owner = new Employee;
            $owner->name = $request->get('contact_name');
            $owner->user_id = $manager->user_id;
            $owner->is_admin = 1;
            $owner->email = $request->get('contact_email');
            $owner->phone = $request->get('contact_phone');
            if (!$desigadmin) {
                $owner->designation = $designation->id;
                $owner->password = $designation->id;
            } else {
                $owner->designation = $desigadmin->id;
                $owner->password = $desigadmin->id;
            }
            if ($request->get('is_active') == 2)
                $stat = 'Active';
            else
                $stat = 'Inactive';
            $owner->status = $stat;
            $owner->company_id = $company->id;
            $save_owner = $owner->save();

            $employee_id = $owner->id;
            $company_id = $request->id;

            DB::table('announce_employee')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('beatvplans')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('clients')
                ->where('company_id', $company_id)->where('created_by', 0)->update(['created_by' => $employee_id]);

            DB::table('collections')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('day_remarks')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('expenses')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('handles')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('images')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('leaves')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('meetings')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('notifications')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('no_orders')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('orders')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('returns')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('stocks')
                ->where('company_id', $company_id)->where('employee_id', 0)->update(['employee_id' => $employee_id]);

            DB::table('activities')
                ->where('company_id', $company_id)->where('assigned_to', 0)->update(['assigned_to' => $employee_id]);

            DB::table('activities')
                ->where('company_id', $company_id)->where('created_by', 0)
                ->update(['created_by' => $employee_id]);

            DB::table('tasks')
                ->where('company_id', $company_id)->where('assigned_from', 0)
                ->update(['assigned_from' => $employee_id]);

            DB::table('tasks')
                ->where('company_id', $company_id)->where('assigned_to', 0)
                ->update(['assigned_to' => $employee_id]);

        }

        $leaveTypeNames = [];
        $leaveTypeNames = ['Sick Leave', 'Maternity Leave', 'Family Responsibility Leave', 'Study Leave', 'Leave for religious holidays', 'Annual Leave', 'Compassionate Leave', 'Paternity Leave', 'Leave in Lieu', 'Miscellaneous/Others'];

        foreach ($leaveTypeNames as $leaveTypeName) {
            $existLeaveType = \App\LeaveType::where('company_id', $company->id)->where('name', $leaveTypeName)->first();
            if (!$existLeaveType) {
                \App\LeaveType::insert([['name' => $leaveTypeName, 'company_id' => $company->id]]);
            }
        }

        $user2 = User::where('id', $manager->user_id)->first();
        $user2->name = $request->get('company_name');
        $user2->email = $request->get('contact_email');
        $user2->phone = $request->get('contact_phone');
        $user2->company_id = $company->id;
        // $user2->is_active = $request->get('is_active');

        if (!empty($request->input('password'))) {
            $this->validate($request, [
                'password' => 'required|min:8',
                'c_password' => 'same:password',

            ], $customMessages);
            $user2->password = bcrypt($request->get('password'));
        }
        $user2->save();


        DB::table('company_plan')->where('company_id', $request->id)->delete();


        //foreach ($request->input('p') as $key => $value) {
        $plan = Plan::where('id', $request->get('plan'))->first();
        $company->plans()->attach($plan);

        $modules = MainModule::leftJoin('plan_has_modules', 'main_modules.id', 'plan_has_modules.module_id')
            ->select('main_modules.*', 'plan_has_modules.id as plan_module_id', 'plan_has_modules.plan_id', 'plan_has_modules.module_id', 'plan_has_modules.enabled')
            ->where('plan_has_modules.plan_id', $plan->id)->get();
        $siteinfo = ClientSetting::where('company_id', $company->id)->first();
        if ($siteinfo) {
            $siteinfo->company_id = $company->id;
            $siteinfo->title = $request->get('company_name');
            $siteinfo->email = $request->get('contact_email');
            $siteinfo->phone = $request->get('contact_phone');
            $siteinfo->ext_no = $request->get('extNo');
            $siteinfo->time_zone = $request->get('time_zone');
            foreach ($modules as $module) {
                $field = $module->field;
                if ($module->enabled == 1) {
                    $siteinfo->$field = 1;

                    // if ($field == 'retailer_app') {
                    //     $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
                    //     if ($role) {
                    //         $role_id = $role->id;
                    //         $permission_category = DB::table('permission_categories')->whereName('outlet')->whereDisplayName('Outlet Connection')->first();
                    //         if ($permission_category) {
                    //             $permission_category_id = $permission_category->id;
                    //             $permissions = DB::table('permissions')->wherePermissionCategoryId($permission_category_id)->pluck('id')->toArray();
                    //             if (!empty($permissions)) {
                    //                 $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                    //                 $non_exist_role_permission = array_merge($permissions, $exist_role_permissions);
                    //                 if (!empty($non_exist_role_permission)) {
                    //                     $role->syncPermissions($non_exist_role_permission);
                    //                 }
                    //             }
                    //         }
                    //     }

                    // }

                    // if ($field == 'visit_module') {
                    //     $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
                    //     $permission_category_visit_module = DB::table('permission_categories')->where('name', 'LIKE', '%Party_Visit%')->first();
                    //     if ($permission_category_visit_module) {
                    //         $permission_category_visit_module_id = $permission_category_visit_module->id;
                    //         $permissions_visit_module = DB::table('permissions')->wherePermissionCategoryId($permission_category_visit_module_id)->pluck('name')->toArray();
                    //         foreach ($permissions_visit_module as $permission_name) {
                    //             $role->givePermissionTo($permission_name);
                    //         }
                    //     }
                    // }

                    // if ($field == 'custom_module') {
                    //     $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
                    //     $role->givePermissionTo('Custom-Module-view');
                    // }

                    // if ($field == 'party_files' || $field == 'party_images') {
                    //     $permission_category_name = $field == 'party_images' ? 'image_uploads' : 'file_uploads';
                    //     $this->enableForFullAccess($company_id, $field, $permission_category_name);
                    // }

                } else {
                    $siteinfo->$field = 0;
                    if ($field == 'product' || $field == 'orders') {
                        $siteinfo->party_wise_rate_setup = 0;
                    }
                }
            }
            $save_setting = $siteinfo->save();
        }

        if ($request->get('is_active') == "0" || $request->get('is_active') == "1") {

            $logout_fbIDs = DB::table('employees')->where(array(array('company_id', $company->id)))->whereNotNull('firebase_token')->pluck('firebase_token');

            $logout_dataPayload = array("data_type" => "employee", "employee" => null, "action" => "logout");
            $logout_sent = sendPushNotification_($logout_fbIDs, 31, null, $logout_dataPayload);
        }

        $fbIDs = DB::table('employees')->where(array(array('company_id', $company->id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($siteinfo), "action" => "update");
        $sent = sendPushNotification_($fbIDs, 12, null, $dataPayload);
        $limited_access_assign_permission_name = array('PartyVisit-view', 'PartyVisit-create');
        // AssignFullAccessPermission::dispatch($company->id, null);
        // AssignLimitedAccessPermission::dispatch($company->id, $limited_access_assign_permission_name);

        //}

        return redirect()->route('app.company')->with('success', 'Information has been  Updated. Setting up roles and permission may take few minutes.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $company = Company::findOrFail($request->id);
        $company->delete();
        flash()->success('Post has been deleted.');
        return back();
    }


    public function settings(Request $request, $id)
    {

        if ($request->id)
            $company_id = $request->id;
        else
            $company_id = $id;


//        $custom_module = DB::table('client_settings')->where('company_id', $company_id)->pluck('custom_module')->first();
        $customModuleCount = CustomModule::where('company_id', $company_id)->count();
        $noOfMinCustomModule = $customModuleCount > 0 ? $customModuleCount : 1;
        $noOfCustomModule = ClientSetting::where('company_id', $id)->pluck('no_of_custom_module')->first();
        // $createPermission = $this->syncOlderPartyTypeRolePermission($company_id);

        //dump($createPermission);

        // $this->syncOlderPartyTypeRolePermission($company_id);

        $companyPlan = DB::table('company_plan')->where('company_id', $company_id)->first();
        if ($companyPlan) $plan = DB::table('plans')->where('id', $companyPlan->plan_id)->first();
        else $plan = null;
        $plans = Plan::where(function ($q) use ($id) {
            $q = $q->where('custom', 0)->orWhere('custom', $id);
        })->where('status', 'Active')->get();
        $active_users = Employee::where('company_id', $company_id)->where("status", "=", "Active")->count();
        $inactive_users = Employee::where('company_id', $company_id)->where("status", "=", "Inactive")->count();
        // $archived_users = DB::table('employees')->where('company_id', $company_id)->where('status', "Archived" )->count();

        $taxes = TaxType::where('company_id', $company_id)->get();//DB::table('tax_types')->where('company_id', $company_id)->get();

        $clientSettings = ClientSetting::where('company_id', $company_id)->first();
        $countries = DB::table('countries')->get();
        if ($clientSettings && $clientSettings->country) {
            $states = DB::table('states')->get()->where('country_id', $clientSettings->country);
        } else {
            $states = array();
        }

        if ($clientSettings && $clientSettings->state) {
            $cities = DB::table('cities')->get()->where('state_id', $clientSettings->state);
        } else {
            $cities = array();
        }
        $zones_array = array();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            // date_default_timezone_set($zone);
            $timezonelist[$zone] = '(UTC/GMT ' . date('P', $timestamp) . ') ' . $zone;
        }

        $currencies = DB::table('currency')->get();

        $partytypes = PartyType::where('parent_id', '=', 0)->where('company_id', $company_id)->get();
        $allPartiestype = PartyType::where('company_id', $company_id)->orderBy('name', 'ASC')->get();

        $mainmodules = MainModule::orderBy('position', 'ASC')->get();
        foreach ($mainmodules as $module) {
            $field = $module->field;
            try{
              $module->value = $clientSettings->$field;
            }catch(Exception $e){
              $module->value = 0;
              Log::error(array(
                "Module Update",
                $module,
                $field,
                $clientSettings,
              ));
            }
        }

        $file_size = 0;
        $directories = array("products" => "Products", "employees" => "Employees", "party" => "Party", "client_visits" => "Party Visit", "notes" => "Notes", "expense" => "Expense", "collection" => "Collection", "noorders" => "Zero Orders", "collaterals" => "Collaterals");
        $module_space = array();
        $total_usage = 0;
        $company = Company::find($id);

        foreach ($directories as $directory => $title) {
            $file_size = 0;
            if (File::exists('cms/storage/app/public/uploads/' . $company->domain . '/' . $directory)) {
                foreach (File::allFiles('cms/storage/app/public/uploads/' . $company->domain . '/' . $directory) as $file) {
                    // bytes
                    $file_size += $file->getSize();
                }
                $size_in_mb = round($file_size / 1048576, 2);
                $module_space[$title] = number_format($size_in_mb, 2) . " Mb";
                $total_usage += $size_in_mb;
            } else {
                $module_space[$title] = number_format(0, 2) . " Mb";
            }
        }

        $uploaded_images = PartyUploadFolder::whereCompanyId($id)->whereHas('files')->whereType('images')->pluck('id')->toArray();
        $uploaded_files = PartyUploadFolder::whereCompanyId($id)->whereHas('files')->whereType('files')->pluck('id')->toArray();
        $files_usage = PartyUpload::whereIn('party_upload_folder_id', $uploaded_images)->sum('file_size');
        $images_usage = PartyUpload::whereIn('party_upload_folder_id', $uploaded_files)->sum('file_size');

        $file_usage_mb = round($files_usage / 1048576, 2);
        $module_space["Party Files"] = number_format($file_usage_mb, 2) . " Mb";

        $image_usage_mb = round($images_usage / 1048576, 2);
        $module_space["Party Images"] = number_format($image_usage_mb, 2) . " Mb";

        $total_usage += $image_usage_mb + $file_usage_mb;
        $total_usage = number_format($total_usage, 2) . " Mb";

        $min_party_file_size = number_format((($files_usage / 1048576) / 1024), 2);
        $min_party_image_size = number_format((($images_usage / 1048576) / 1024), 2);

        // DefaultLevelValues::dispatch();
        $current_designation_hierarchy = Designation::whereCompanyId($company_id)->orderBy('parent_id', 'desc')->CompanyMaxDesignationLevel();
        if($current_designation_hierarchy < 1) $current_designation_hierarchy = 2;
        $current_party_type_hierarchy = PartyType::whereCompanyId($company_id)->orderBy('parent_id', 'desc')->CompanyMaxPartyTypeLevel();
        if($current_party_type_hierarchy < 1) $current_party_type_hierarchy = 1;
        $current_roles = Role::whereCompanyId($company_id)->count();
        
        return view('admin.companies.settings.edit', compact('timezonelist', 'company', 'countries', 'states', 'cities', 'plans', 'plan', 'active_users', 'inactive_users', 'taxes', 'currencies', 'partytypes', 'allPartiestype', 'mainmodules', 'clientSettings', 'noOfCustomModule', 'module_space', 'noOfMinCustomModule', 'total_usage', 'min_party_file_size', 'min_party_image_size', 'current_designation_hierarchy', 'current_party_type_hierarchy', 'current_roles'));
    }

    public function updateProfile(Request $request)
    {
        $customMessages = [
            'title.required' => 'The Title field is required.',
            'phone.required' => 'Phone number is required',
            'mobile.required' => 'Mobile number is required',
            'phone.digits_between' => 'Phone must be between 7 to 14 digits',
            'mobile.digits_between' => 'Mobile must be between 7 to 14 digits',
            'city' => 'City field is required',
            'state' => 'State field is required',
            'country' => 'Country field is required',
        ];

        $this->validate($request, [
            // 'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            // 'default_currency' => 'required',
            // 'time_zone' => 'required',
            // 'date_format' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
        ], $customMessages);

        $siteinfo = ClientSetting::findOrFail($request->id);

        $company = Company::findOrFail($siteinfo->company_id);
        $companyName = $company->domain;


        if (!empty($request->get('title'))) {
            $siteinfo->title = $request->get('title');
        }
        // $siteinfo->email=$request->get('email');
        $siteinfo->phonecode = $request->get('phonecode');
        // $siteinfo->phone = $request->get('phone');
        // $siteinfo->ext_no = $request->get('ext_no');
        $siteinfo->mobile = $request->get('mobile');
        $siteinfo->opening_time = $request->get('opening_time');
        $siteinfo->closing_time = $request->get('closing_time');
        $siteinfo->address_1 = $request->get('address_1');
        $siteinfo->address_2 = $request->get('address_2');
        $siteinfo->city = $request->get('city');
        $siteinfo->state = $request->get('state');
        $siteinfo->country = $request->get('country');
        $siteinfo->zip = $request->get('zip');

        if ($request->file('logo')) {
          if (!empty($siteinfo->logo_path) && file_exists(base_path() . $siteinfo->logo_path)) {
          }

          $this->validate($request, [
                      'logo' => 'mimes:jpeg,png,jpg|max:500'
                  ]);

          $image = $request->file('logo');
          $realname = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $image->getClientOriginalExtension();
          $new_name = $realname . "-" . time() . '.' . $extension;
          $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
          $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
          $siteinfo->logo = $new_name;
          $siteinfo->logo_path = $path;
      }


        if ($request->file('small_logo')) {
            $this->validate($request, [
                'small_logo' => 'mimes:jpeg,png,jpg|max:300'
            ]);

            $image = $request->file('small_logo');
            $realname = pathinfo($request->file('small_logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->small_logo = $new_name;
            $siteinfo->small_logo_path = $path;
        }


        if ($request->file('favicon')) {
            $this->validate($request, [
                'favicon' => 'mimes:jpeg,png,jpg,ico|max:100'
            ]);
            $image2 = $request->file('favicon');
            $realname2 = pathinfo($request->file('favicon')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension2 = $image2->getClientOriginalExtension();
            $new_name2 = $realname2 . "-" . time() . '.' . $extension2;
            $image2->storeAs('public/uploads/' . $companyName . '/logo/', $new_name2);
            $path2 = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name2);
            $siteinfo->favicon = $new_name2;
            $siteinfo->favicon_path = $path2;
        }


        if (isset($siteinfo->country) && $siteinfo->country != "--Country--") {
            $getCountryName = getCountryName($siteinfo->country)->name;
        } else {
            $getCountryName = "";
        }

        if (isset($siteinfo->state) && $siteinfo->state != "--State--") {
            $getStateName = getStateName($siteinfo->state)->name;
        } else {
            $getStateName = "";
        }

        if (isset($siteinfo->city) && $siteinfo->state != "--City--") {
            $getCityName = getCityName($siteinfo->city);
        } else {
            $getCityName = "";
        }

        if (isset($getCountryName)) {
            $address = $getCountryName;
            if (isset($getStateName))
                $address = $getStateName . ', ' . $address;
            if (isset($getCityName))
                $address = $getCityName . ', ' . $address;
            $latLng = $this->getLatLng($address);
            if (!empty($latLng)) {
                $siteinfo->latitude = "$latLng->lat";
                $siteinfo->longitude = "$latLng->lng";
            }
        }
        $siteinfo->save();
        $userInstance = Manager::where('company_id', $siteinfo->company_id)->where('is_owner', 1)->first(['user_id']);
        $employeeInstance = Employee::where('company_id', $siteinfo->company_id)->where('user_id', $userInstance->user_id)->first();
        $employeeInstance->country_code = $request->get('phonecode');
        $employeeInstance->save();
        $request->session()->flash('active', 'profile');
        return redirect()->back();
    }

    private function getLatLng($address)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8';
        $geocode = @file_get_contents($url);
        $json = json_decode($geocode);
        $status = $json->status;
        if ($status == "OK") {
            return $json->results[0]->geometry->location;
        } else {
            return null;
        }
    }

    public function updateLayout(Request $request)
    {
        $siteinfo = ClientSetting::findOrFail($request->id);

        $company = Company::findOrFail($siteinfo->company_id);
        $companyName = $company->domain;

        $siteinfo->login_title = $request->get('login_title');
        $siteinfo->login_description = $request->get('login_description');

        if ($request->file('logo')) {

            if (!empty($siteinfo->logo_path) && file_exists(base_path() . $siteinfo->logo_path)) {
            }

            $this->validate($request, [
                'logo' => 'mimes:jpeg,png,jpg|max:500'
            ]);

            $image = $request->file('logo');
            $realname = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->logo = $new_name;
            $siteinfo->logo_path = $path;
        }


        if ($request->file('small_logo')) {

            $this->validate($request, [
                'small_logo' => 'mimes:jpeg,png,jpg|max:300'
            ]);

            $image = $request->file('small_logo');
            $realname = pathinfo($request->file('small_logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->small_logo = $new_name;
            $siteinfo->small_logo_path = $path;
        }


        if ($request->file('favicon')) {
            $this->validate($request, [
                'favicon' => 'mimes:jpeg,png,jpg,ico|max:100'
            ]);
            $image2 = $request->file('favicon');
            $realname2 = pathinfo($request->file('favicon')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension2 = $image2->getClientOriginalExtension();
            $new_name2 = $realname2 . "-" . time() . '.' . $extension2;
            $image2->storeAs('public/uploads/' . $companyName . '/logo/', $new_name2);
            $path2 = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name2);
            $siteinfo->favicon = $new_name2;
            $siteinfo->favicon_path = $path2;
        }


        $siteinfo->save();
        $request->session()->flash('active', 'layout');
        return redirect()->back();
    }

    public function updateEmail(Request $request)
    {

        $siteinfo = ClientSetting::findOrFail($request->id);

        $siteinfo->smtp_host = $request->get('smtp_host');
        $siteinfo->smtp_username = $request->get('smtp_username');

        if (!empty($request->get('smtp_password')))
            $siteinfo->smtp_password = $request->get('smtp_password');

        $siteinfo->smtp_port = $request->get('smtp_port');

        $siteinfo->invoice_mail_from = $request->get('invoice_mail_from');
        $siteinfo->recovery_mail_from = $request->get('recovery_mail_from');
        $siteinfo->other_mails_from = $request->get('other_mails_from');

        $siteinfo->save();
        $request->session()->flash('active', 'email');
        return redirect()->back();
    }

    public function updateOther(Request $request)
    {
        $customMessage = [
            'total_file_size_gb.min' => "Minimum size of " . $request->allowed_min_file_size_gb . " GB has already been used.",
            'total_image_size_gb.min' => "Minimum size of " . $request->allowed_min_image_size_gb . " GB has already been used.",
        ];
        $this->validate($request, [
            'total_file_size_gb' => 'min:' . $request->allowed_min_file_size_gb,
            'total_image_size_gb' => 'min:' . $request->allowed_min_image_size_gb
        ], $customMessage);
        $siteinfo = ClientSetting::findOrFail($request->id);
        $company_id = $siteinfo->company_id;

        $customModuleCount = CustomModule::where('company_id', $company_id)->count();
        if (isset($request->no_of_custom_module)) {
            if ($request->get('no_of_custom_module') >= $customModuleCount) {
                $siteinfo->no_of_custom_module = $request->get('no_of_custom_module');
            } else {
                \Session::flash("warning", "Number of Custom Module cannot be less than current number.");
            }
        }


        $siteinfo->default_currency = $request->get('default_currency');
        $siteinfo->currency_format = $request->get('currency_format');
        $siteinfo->currency_symbol = $request->get('currency_symbol');
//        $siteinfo->schemes = $request->get('schemes'); used from main modules
//        $siteinfo->pdf_language = $request->get('pdf_language');

        $siteinfo->time_zone = $request->get('time_zone');
        // $siteinfo->date_format = $request->get('date_format');
        $siteinfo->date_type = $request->get('date_type');

        // $siteinfo->invoice_prefix = $request->get('invoice_prefix');
        $siteinfo->order_prefix = $request->get('order_prefix');
        $siteinfo->order_with_amt = $request->get('order_with_amt');
        // $siteinfo->order_to = $request->get('order_to');
        $siteinfo->order_approval = $request->get('order_approval');
        $siteinfo->brand = $request->get('brand');
        $siteinfo->order_with_authsign = $request->get('order_with_authsign');
        $siteinfo->ncal = $request->get('ncal');
        $siteinfo->non_zero_discount = $request->get('non_zero_discount');
        $siteinfo->product_level_tax = $request->get('product_level_tax');
        $siteinfo->product_level_discount = $request->get('product_level_discount');
        $siteinfo->party_wise_rate_setup = $request->get('party_wise_rate_setup');
        $siteinfo->category_wise_rate_setup = $request->get('category_wise_rate_setup');
        if($siteinfo->category_wise_rate_setup == 1 && $siteinfo->party_wise_rate_setup == 1 ){
          $siteinfo->party_wise_rate_setup = 0;
          $siteinfo->category_wise_rate_setup = 0;
        }
        $siteinfo->unit_conversion = $request->get('unit_conversion');
        $siteinfo->var_colors = $request->get('var_colors');
        
        $siteinfo->user_hierarchy_level = $request->get('user_hierarchy_level');
        $current_designation_hierarchy = Designation::whereCompanyId($company_id)->orderBy('parent_id', 'desc')->CompanyMaxDesignationLevel();
        if($siteinfo->user_hierarchy_level < $current_designation_hierarchy){
          $siteinfo->user_hierarchy_level = $current_designation_hierarchy;
        }
        $siteinfo->allowed_party_type_levels = $request->get('allowed_party_type_levels');
        $current_party_type_hierarchy = PartyType::whereCompanyId($company_id)->orderBy('parent_id', 'desc')->CompanyMaxPartyTypeLevel();
        if($current_party_type_hierarchy > $siteinfo->allowed_party_type_levels){
          $siteinfo->allowed_party_type_levels = $current_party_type_hierarchy;
        }
        $siteinfo->user_roles = $request->get('user_roles');
        $current_roles = Role::whereCompanyId($company_id)->count();
        if($current_roles > $siteinfo->user_roles){
          $siteinfo->user_roles = $current_roles;
        }

        
        // $siteinfo->allow_party_duplication = $request->get('allow_party_duplication');
        $uploadTypes = $request->get('upload_types');
        $siteinfo->total_collaterals_size_gb = $request->total_collaterals_size_gb;
        // $siteinfo->tally = $request->get('tally_integration');

        // $tallyInt = DB::table('tally_schedule')->where('company_id', $company_id)->first();
        // $companyname = DB::table('companies')->where('id', $company_id)->first();

        // if ($request->get('tally_integration') == 0 && !empty($tallyInt)) {
        //     $affected = DB::table('tally_schedule')
        //         ->where('company_id', $company_id)
        //         ->update(['updated_at' => date('Y-m-d H:i:s'), 'deleted_at' => date('Y-m-d H:i:s')]);
        // } elseif ($request->get('tally_integration') == 1 && empty($tallyInt)) {
        //     $affected = DB::table('tally_schedule')->insert([
        //         'company_id' => $company_id,
        //         'company_name' => $companyname->domain,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ]);
        // } elseif ($request->get('tally_integration') == 1 && !empty($tallyInt)) {
        //     $affected = DB::table('tally_schedule')
        //         ->where('company_id', $company_id)
        //         ->update(['updated_at' => date('Y-m-d H:i:s'), 'deleted_at' => NULL]);
        // }

        if (!empty($uploadTypes)) {
            if (in_array("doc,docx,odt", $uploadTypes) || in_array("csv,xlsx,xls", $uploadTypes)) {
                array_push($uploadTypes, "zip");
                if (in_array("csv,xlsx,xls", $uploadTypes)) {
                    array_push($uploadTypes, "txt");
                }
            }
            $siteinfo->uploadtypes = (($uploadTypes) && ($request->file_upload_size)) ? implode(',', $uploadTypes) : NULL;

            $siteinfo->file_upload_size = (($uploadTypes) && ($request->file_upload_size)) ? $request->file_upload_size : NULL;
        }

        // $siteinfo->party_files_images = $request->get('party_files_images');
        // if($siteinfo->party_files_images == 1){
        //   $this->enableForFullAccess($company_id, 'party_files_images');
        // }
        $partyFileUploadTypes = $request->get('party_file_upload_types');
        if (!empty($partyFileUploadTypes)) {
            $siteinfo->party_file_upload_types = (($partyFileUploadTypes) && ($request->party_file_upload_types)) ? implode(',', $partyFileUploadTypes) : NULL;
        }
        $siteinfo->party_file_upload_size = $request->party_file_upload_size;

        $partyImageUploadTypes = $request->get('party_image_upload_types');
        if (!empty($partyImageUploadTypes)) {
            $siteinfo->party_image_upload_types = (($partyImageUploadTypes) && ($request->party_image_upload_types)) ? implode(',', $partyImageUploadTypes) : NULL;
        }
        $siteinfo->party_image_upload_size = $request->party_image_upload_size;

        $siteinfo->total_file_size_gb = $request->total_file_size_gb;
        $siteinfo->total_image_size_gb = $request->total_image_size_gb;

        if (!empty($request->get('tax_name')) && !empty($request->get('tax_percent'))) {
            $tax_type = [];
            $names = $request->get('tax_name');
            $percents = $request->get('tax_percent');
            $defaultTax = $request->get('defaultTax');
            $i = 0;
            foreach ($names as $index => $name) {
                if (!empty($name) && !empty($percents[$index])) {
                    $tax_type[] = [
                        'company_id' => $company_id,
                        'name' => $name,
                        'percent' => $percents[$index],
                        'default_flag' => !empty($defaultTax) ? array_key_exists($index, $defaultTax) ? 1 : 0 : 0
                    ];
                    $i++;
                }
            }
            TaxType::insert($tax_type);
            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent', 'default_flag', 'deleted_at')->where('company_id', $company_id)->whereNull('deleted_at')->get();
            $dataPayload = array("data_type" => "tax", "tax" => json_encode($taxTypes), "action" => "add");
            $sent = sendPushNotification_($fbIDs, 30, null, $dataPayload);

        }

        $saved = $siteinfo->save();


        //notify all employee apps   , need to optimize
        if ($saved) {

            $fbIDs = DB::table('employees')->where(array(array('company_id', $siteinfo->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($siteinfo), "action" => "update");
            $sent = sendPushNotification_($fbIDs, 12, null, $dataPayload);
        }


        $request->session()->flash('active', 'other');
        return redirect()->back();
    }

    private function enableForFullAccess($company_id, $module, $permission_category_name)
    {
        $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
        // $permission_category_party_uploads_modules = DB::table('permission_categories')->where( function($q){
        //   $q->orWhere('name', 'LIKE', 'file_uploads');
        //   $q->orWhere('name', 'LIKE', 'image_uploads');
        // })->get();
        $permission_category_party_uploads_modules = DB::table('permission_categories')->where('name', 'LIKE', $permission_category_name)->get();
        if ($permission_category_party_uploads_modules->first()) {
            try {
                foreach ($permission_category_party_uploads_modules as $permission_category_party_uploads_module) {
                    $permission_category_party_uploads_module_id = $permission_category_party_uploads_module->id;
                    $permissions_partyuploads = DB::table('permissions')->wherePermissionCategoryId($permission_category_party_uploads_module_id)->pluck('name')->toArray();
                    foreach ($permissions_partyuploads as $permission_name) {
                        $role->givePermissionTo($permission_name);
                    }
                }
            } catch (\Exception $e) {
                Log::info($e->getCode());
            }
            return true;
        }
    }

    public function removeTax(Request $request)
    {
        $instance = TaxType::find($request->tax_id);
        $instance->products()->detach();
        $deleted = $instance->delete();
        if ($deleted) {
            $fbIDs = DB::table('employees')->where(array(array('company_id', $instance->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent', 'default_flag', 'deleted_at')->where('company_id', $instance->company_id)->whereNull('deleted_at')->get();
            $dataPayload = array("data_type" => "tax", "tax" => json_encode($taxTypes), "action" => "delete");
            $sent = sendPushNotification_($fbIDs, 30, null, $dataPayload);

            return response()->json([
                'message' => 'Deleted Successfully.',
                'code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Failed Deleting Tax Type.',
                'code' => 400
            ]);
        }
    }

    public function updateDefaultFlag(Request $request)
    {
        $instance = TaxType::findOrFail($request->taxId);
        $company_id = $instance->company_id;
        $instance->default_flag = $request->flagVal;
        $updated = $instance->update();
        if ($updated) {
            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent', 'default_flag', 'deleted_at')->where('company_id', $company_id)->whereNull('deleted_at')->get();
            $dataPayload = array("data_type" => "tax", "tax" => json_encode($taxTypes), "action" => "add");
            $sent = sendPushNotification_($fbIDs, 30, null, $dataPayload);

            return response()->json([
                'message' => 'Updated Successfully.',
                'code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Update Failed.',
                'code' => 400
            ]);
        }
    }

    public function updateTax(Request $request)
    {
        $tax_name = $request->tax_name;
        $tax_percent = $request->tax_percent;
        $update = false;
        $instance = TaxType::findOrFail($request->taxId);
        $company_id = $instance->company_id;
        if ($tax_percent != $instance->percent || $tax_name != $instance->name) $update = true;
        $instance_copy = $instance->toArray();
        $instance->name = $tax_name;
        $instance->percent = $tax_percent;
        $updated = $instance->update();
        $oldOrderInstance = $instance->orders;
        $oldOrderDetailsInstance = $instance->orderdetails;
        $oldProductsInstance = $instance->products;
        if ($update) {
            if ($oldOrderInstance->count() > 0 || $oldOrderDetailsInstance->count() > 0 || $oldProductsInstance->count() > 0) {
                $timestamp = date("Y-m-d h:i:s");
                $inserted = TaxType::create([
                    "company_id" => $company_id,
                    "chained_to" => $request->taxId,
                    "name" => $instance_copy["name"],
                    "percent" => $instance_copy["percent"],
                    "default_flag" => $instance_copy["default_flag"],
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                    "deleted_at" => $timestamp,
                ]);
                $insertedId = $inserted->id;
                if ($oldOrderInstance->count() > 0) {
                    $instance->orders()->update([
                        'tax_type_id' => $insertedId,
                        "update_at" => $timestamp,
                    ]);
                }

                if ($oldOrderDetailsInstance->count() > 0) {
                    $instance->orderdetails()->update([
                        'tax_type_id' => $insertedId
                    ]);
                }

                // if($oldProductsInstance->count()>0){
                //   $instance->products()->update([
                //     'tax_type_id' => $insertedId
                //   ]);
                // }
            }
        }
        if ($updated) {
            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent', 'default_flag', 'deleted_at')->where('company_id', $company_id)->whereNull('deleted_at')->get();
            $dataPayload = array("data_type" => "tax", "tax" => json_encode($taxTypes), "action" => "add");
            $sent = sendPushNotification_($fbIDs, 30, null, $dataPayload);

            return response()->json([
                'message' => 'Updated Successfully.',
                'code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Update Failed.',
                'code' => 400
            ]);
        }
    }

    public function addPartyType(Request $request)
    {
        try {
            DB::BeginTransaction();

            $clientSetting = ClientSetting::where('id', $request->get('company_id'))->first();
            $company_id = $clientSetting->company_id;
            $this->validate($request, [
                'name' => 'required',
            ]);
            $partyTypeExists = PartyType::where('company_id', $company_id)->where('name', $request->name)->first();
            if ($partyTypeExists)
                return redirect()->back()->with(['error' => 'Party Type already exists.']);
            $input['name'] = $request->get('name');
            $input['short_name'] = $request->get('short_name');
            $input['company_id'] = $clientSetting['company_id'];
            $input['parent_id'] = empty($request->get('parent_id')) ? 0 : $request->get('parent_id');
            if ($request->display_status == 'on') {
                $input['allow_salesman'] = 1;
            } else {
                $input['allow_salesman'] = 0;
            }
            $partytype = PartyType::create($input);

            $permissionCategory = new PermissionCategory;
            $permissionCategory->company_id = $company_id;
            $permissionCategory->permission_model_id = $partytype->id;
            $permissionCategory->permission_model = 'PartyType';
            $permissionCategory->permission_category_type = 'Company';
            $permissionCategory->name = str_replace(' ', '_', $partytype->name);
            $permissionCategory->display_name = 'Party Type: ' . $partytype->name;
            $permissionCategory->indexing_priority = 10;
            $permissionCategory->save();

            $create_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'create');
            $view_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'view');
            $update_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'update');
            $delete_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'delete');
            $status_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'status');

            DB::Commit();

            $party_type_permissions = array($create_id, $view_id, $update_id, $delete_id, $status_id);

            $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
            if ($role) {
                $role_id = $role->id;
                $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                $non_exist_role_permission = array_merge($party_type_permissions, $exist_role_permissions);
                if (!empty($non_exist_role_permission)) {
                    $role->syncPermissions($non_exist_role_permission);
                }
            }

            $data = array();
            $non_permitted_users_data = array();

            $permissions = Permission::where('is_mobile', 1)->where('permission_category_id', $permissionCategory->id)->where('permission_type', 'Company')->where('company_id', $company_id)->get();

            foreach ($permissions as $permission) {
                $data['pt-' . $permission->name] = ($role->hasPermissionTo($permission->id)) ? '1' : '0';
                $non_permitted_users_data['pt-' . $permission->name] = '0';
            }

            if ($partytype) {
                $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $role->id)->pluck('firebase_token');
                $partytypes = PartyType::find($partytype->id);
                $dataPayload = array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "permissions" => $data, "action" => "add");
                $sent = sendPushNotification_($fbIDs, 22, null, $dataPayload);

                $fbIds = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', '!=', $role->id)->pluck('firebase_token');
                $dataPayLoad = array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "permissions" => $non_permitted_users_data, "action" => "add");
                $data_sent = sendPushNotification_($fbIds, 22, null, $dataPayLoad);

            }
            return back()->with('success', 'Party type has been added successfully. Please setup roles and permissions for this party type.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'something went wrong' . $e->getMessage());
        }

    }

    public function addMarketArea(Request $request)
    {
        $company_id = ClientSetting::where('id', $request->get('company_id'))->first();
        $this->validate($request, [
            'name' => 'required',
        ]);
        $input['name'] = $request->get('name');
        $input['company_id'] = $company_id['company_id'];
        $input['parent_id'] = empty($request->get('parent_id')) ? 0 : $request->get('parent_id');

        MarketArea::create($input);
        return back()->with('success', 'New Area added successfully.');
    }

    public function editMarketArea($id, Request $request)
    {
        $company_id = ClientSetting::where('id', $request->get('company_id'))->first();
        $marketarea = MarketArea::findOrFail($id);
        $data['company_id'] = $company_id['company_id'];
        $marketarea['name'] = $request->get('area_name');
        $marketarea['parent_id'] = $request->area_parent;
        $marketarea->save();
        $data['marketareas'] = MarketArea::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
        $view = View::make('ajaxmarketarea', $data);
        return response()->json(['message' => 'Party Type Updated Successfully.', 'url' => route('app.company.setting', ['id' => $data['company_id']]), 'view' => $view->render()], 200);

    }

    public function removeMarketArea($id, Request $request)
    {
        $company_id = ClientSetting::where('id', $request->get('company_id'))->first();
        $marketArea = MarketArea::findOrFail($id);
        $data['company_id'] = $company_id['company_id'];
        if ($marketArea->company_id == $data['company_id']) {
            foreach ($marketArea->childs as $child) {
                $child->delete();
            }
            $marketArea->delete();
        }
        $data['marketareas'] = MarketArea::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
        $view = View::make('ajaxmarketarea', $data);
        return response()->json(['message' => 'Market Area Deleted Successfully.', 'url' => route('app.company.setting', ['id' => $company_id]), 'view' => $view->render()], 200);
    }

    public function removePartyType($id, Request $request)
    {
        try {
            DB::BeginTransaction();
            $clientSetting = ClientSetting::where('id', $request->get('company_id'))->first();
            $company_id = $clientSetting->company_id;
            $partyType = PartyType::findOrFail($id);
            $clientExists = Client::where('client_type', $id)->first();
            // Log::info('info', array("data"=>print_r($clientExists,true)));
            $data['company_id'] = $clientSetting['company_id'];
            if ($partyType->company_id == $company_id && !$clientExists) {
                foreach ($partyType->childs as $child) {
                    $child->delete();
                }
                $partytype = $partyType->delete();

                $permissionCategory = PermissionCategory::where('permission_model', 'PartyType')
                    ->where('permission_model_id', $id)->first();
                $oldPermissionCategory = $permissionCategory;
                $permissionCategory->delete();

                $this->destroyPermission($company_id, $oldPermissionCategory, 'create');
                $this->destroyPermission($company_id, $oldPermissionCategory, 'view');
                $this->destroyPermission($company_id, $oldPermissionCategory, 'update');
                $this->destroyPermission($company_id, $oldPermissionCategory, 'delete');
                $this->destroyPermission($company_id, $oldPermissionCategory, 'status');

                if ($partytype) {

                    $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
                    $partytypes = PartyType::where('company_id', $company_id)->get();
                    $dataPayload = array("data_type" => "partytype", "type_id" => $id, "party_type" => null, "action" => "delete");
                    $sent = sendPushNotification_($fbIDs, 22, null, $dataPayload);

                }
            }

            DB::Commit();
            $data['partytypes'] = PartyType::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
            $view['tree'] = View::make('ajaxpartytype', $data)->render();
            $view['partytypes'] = PartyType::where('company_id', $data['company_id'])->orderBy('name', 'ASC')->get();
            return $view;

        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error : ' . $e->getMessage()], 500);
        }
    }

    public function editPartyType($id, Request $request)
    {

        try {
            DB::BeginTransaction();

            $clientSetting = ClientSetting::where('id', $request->get('company_id'))->first();
            $company_id = $clientSetting->company_id;

            $partyType = PartyType::findOrFail($id);
            $partyTypeExists = PartyType::where('company_id', $company_id)->where('name', $request->party_type)->where('id', '!=', $partyType->id)->first();
            if ($partyTypeExists)
                return response(['error' => 'Party Type already exists.']);
            $oldPartyType = $partyType->replicate();
            $data['company_id'] = $clientSetting['company_id'];
            $partyType['name'] = $request->party_type;
            $partyType['short_name'] = $request->party_type_short_name;
            if (!empty($request->party_parent)) {
                $partyType['parent_id'] = $request->party_parent;
            } else {
                $partyType['parent_id'] = $partyType->parent_id;
            }
            if ($request->display_status == 1) {
                $partyType['allow_salesman'] = 1;
            } else {
                $partyType['allow_salesman'] = 0;
            }
            $partytype = $partyType->save();

            $permissionCategory = PermissionCategory::where('permission_model', 'PartyType')
                ->where('permission_model_id', $partyType->id)->first();
            $oldPermissionCategory = $permissionCategory;
            $permissionCategory->name = str_replace(' ', '_', $partyType->name);
            $permissionCategory->display_name = 'Party Type: ' . $partyType->name;
            $permissionCategory->indexing_priority = 10;
            $permissionCategory->save();

            $this->updatePermission($company_id, $partyType, $oldPartyType, $oldPermissionCategory, 'create');
            $this->updatePermission($company_id, $partyType, $oldPartyType, $oldPermissionCategory, 'view');
            $this->updatePermission($company_id, $partyType, $oldPartyType, $oldPermissionCategory, 'update');
            $this->updatePermission($company_id, $partyType, $oldPartyType, $oldPermissionCategory, 'delete');
            $this->updatePermission($company_id, $partyType, $oldPartyType, $oldPermissionCategory, 'status');

            DB::Commit();

            $name = str_replace(' ', '-', $permissionCategory->name);
            $permissions = Permission::where('is_mobile', 1)->where('permission_category_id', $oldPermissionCategory->id)->where('permission_type', 'Company')->where('name', 'LIKE', $name . '-%')->where('company_id', $company_id)->get();

            $pdata = array();

            $allroles_with_permission = DB::table('role_has_permissions')->whereIn('permission_id', $permissions->pluck('id')->toArray())->pluck('role_id')->toArray();

            $roles_with_permission = Role::whereIn('id', $allroles_with_permission)->get();
            if ($roles_with_permission->first()) {
                $permissions_data = array();
                if ($permissions->first()) {
                    foreach ($roles_with_permission as $role) {
                        foreach ($permissions as $permission) {
                            $pdata['pt-' . $permission->name] = ($role->hasPermissionTo($permission->id)) ? '1' : '0';
                        }
                        $permissions_data[$role->id] = $pdata;
                    }
                }
            }


            if ($partytype) {

                // $fbIDs      = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('role', $allroles_with_permission)->pluck('firebase_token', 'role');
                $partytypes = PartyType::findOrFail($id);
                foreach ($allroles_with_permission as $prole) {
                    $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $prole)->pluck('firebase_token');
                    $dataPayload = array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "action" => "update", "permissions" => $permissions_data[$prole]);
                    $sent = sendPushNotification_($fbIDs, 22, null, $dataPayload);
                }
            }
            $data['partytypes'] = PartyType::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
            $view['tree'] = View::make('ajaxpartytype', $data)->render();
            $view['partytypes'] = PartyType::where('company_id', $data['company_id'])->orderBy('name', 'ASC')->get();
            return $view;

        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error : ' . $e->getMessage()], 500);
        }
    }

    public function getPartyTypeList(Request $request)
    {
        $company_id = ClientSetting::where('id', $request->get('company_id'))->first();
        $data['company_id'] = $company_id['company_id'];
        $view['partytypes'] = PartyType::where('company_id', $data['company_id'])->orderBy('name', 'ASC')->where('id', '!=', $request->myId)->get();
        return $view;
    }

    // public function empdownload($id){
    //     $employees = Employee::where('company_id',$id)->select('name as Name','password as Password')->where('status','Active')->get()->toArray();
    //     $filename = 'employees_company_id_'.$id;
    //     $type = 'xlsx';
    //     Excel::create($filename, function ($excel) use ($employees) {
    //         $excel->sheet('Employees', function ($sheet) use ($employees) {
    //             $sheet->fromArray($employees);
    //         });
    //     })->download($type);
    // }

    public function empdownload($id)
    {
        $employees = Employee::where('company_id', $id)->select('name as Name', 'password as Password', 'email as Email', 'phone as Phone')->where('status', 'Active')->get()->toArray();
        $filename = 'employees_company_id_' . $id;
        $type = 'xlsx';

        return Excel::download(new EmployeeExports($employees), $filename . '.' . $type);

    }

    public function notes(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        // dd($request->get('company_note'));
        $company->note = $request->get('company_note');
        $company->save();

        \Session::flash('success', 'Note has been added successfully.');

        return back();
    }

    public function updateModule(Request $request, $id)
    {
        $customMessages = [
            'name' => 'The Plan Name field is required.',
        ];

        if ($request->plan_id) {
            $this->validate($request, [
                'name' => 'required|unique:plans,name,' . $request->plan_id,
            ], $customMessages);
        } else {
            $this->validate($request, [
                'name' => 'required|unique:plans',
            ], $customMessages);
        }


        $company = Company::where('id', $id)->first();
        $plan = Plan::where('id', $request->plan_id)->first();
        $mainmodules = MainModule::all();
        if ($plan) {
            DB::table('plan_has_modules')->where('plan_id', $plan->id)->delete();
        } else {
            $plan = new Plan;
        }
        $plan->name = $request->name;
        $plan->description = $request->plan_description;
        $plan->status = 'Active';
        $plan->custom = $id;
        $plan->save();

        $datetime = date('Y-m-d H:i:s');
        $has_tally_integration = $request->has('tally');

        $tallyInt = DB::table('tally_schedule')->where('company_id', $id)->first();

        if (!$has_tally_integration && !empty($tallyInt)) {
            $affected = DB::table('tally_schedule')
                ->where('company_id', $id)
                ->update(['updated_at' => $datetime, 'deleted_at' => $datetime]);
        } elseif ($has_tally_integration) {
            if ($tallyInt) $affected = DB::table('tally_schedule')->where('company_id', $id)->update(['updated_at' => $datetime, 'deleted_at' => NULL]);
            else $affected = DB::table('tally_schedule')->insert([
                'company_id' => $id,
                'company_name' => $company->domain,
                'created_at' => $datetime]);
        }

        foreach ($mainmodules as $module) {
            $field = $module->field;
            if ($request->$field) {
                DB::table('plan_has_modules')->insert(['plan_id' => $plan->id, 'module_id' => $module->id, 'enabled' => 1]);
                // if ($field == 'retailer_app') {
                //     $role = Role::whereCompanyId($company->id)->whereName('Full Access')->whereGuardName('web')->first();
                //     if ($role) {
                //         $role_id = $role->id;
                //         $permission_category = DB::table('permission_categories')->whereName('outlet')->whereDisplayName('Outlet Connection')->first();
                //         if ($permission_category) {
                //             $permission_category_id = $permission_category->id;
                //             $permissions = DB::table('permissions')->wherePermissionCategoryId($permission_category_id)->pluck('id')->toArray();
                //             if (!empty($permissions)) {
                //                 $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                //                 $non_exist_role_permission = array_merge($permissions, $exist_role_permissions);
                //                 if (!empty($non_exist_role_permission)) {
                //                     $role->syncPermissions($non_exist_role_permission);
                //                 }
                //             }
                //         }
                //     }

                // }

                // if ($field == 'visit_module') {
                //     $role = Role::whereCompanyId($company->id)->whereName('Full Access')->whereGuardName('web')->first();
                //     $permission_category_visit_module = DB::table('permission_categories')->where('name', 'LIKE', '%Party_Visit%')->first();
                //     if ($permission_category_visit_module) {
                //         $permission_category_visit_module_id = $permission_category_visit_module->id;
                //         $permissions_visit_module = DB::table('permissions')->wherePermissionCategoryId($permission_category_visit_module_id)->pluck('name')->toArray();
                //         foreach ($permissions_visit_module as $permission_name) {
                //             $role->givePermissionTo($permission_name);
                //         }
                //     }
                // }
                // if ($field == 'custom_module') {
                //     $role = Role::whereCompanyId($company->id)->whereName('Full Access')->whereGuardName('web')->first();
                //     $role->givePermissionTo('Custom-Module-view');
                // }

                // if ($field == 'party_files' || $field == 'party_images') {
                //     $permission_category_name = $field == 'party_images' ? 'image_uploads' : 'file_uploads';
                //     $this->enableForFullAccess($company->id, $field, $permission_category_name);
                // }
            } else {
                DB::table('plan_has_modules')->insert(['plan_id' => $plan->id, 'module_id' => $module->id, 'enabled' => 0]);
            }
        }
        $company_plan = DB::table('company_plan')->where('company_id', $id)->update(['plan_id' => $plan->id]);
        $clientSetting = ClientSetting::where('company_id', $id)->first();
        if ($clientSetting) {
            foreach ($mainmodules as $module) {
                $field = $module->field;
                if ($request->$field) {
                    $clientSetting->$field = 1;
                } else {
                    $clientSetting->$field = 0;

                    if ($field == 'product' || $field == 'orders') {
                        $clientSetting->party_wise_rate_setup = 0;
                    }
                }
            }
            $clientSetting->save();
            $fbIDs = DB::table('employees')->where(array(array('company_id', $id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($clientSetting), "action" => "update");
            $sent = sendPushNotification_($fbIDs, 12, null, $dataPayload);
            $plans = Plan::where(function ($q) use ($id) {
                $q = $q->where('custom', 0)->orWhere('custom', $id);
            })->where('status', 'Active')->get();
            $data = ['status' => true, 'message' => 'Modules Updated Successfully', 'settings' => $clientSetting, 'plans' => $plans];
            // AssignFullAccessPermission::dispatch($id, null);
            // $limited_access_assign_permission_name = array('PartyVisit-view', 'PartyVisit-create');
            // AssignLimitedAccessPermission::dispatch($id, $limited_access_assign_permission_name);

        } else {
            $data = ['status' => false, 'message' => 'Modules Updated Failed'];
        }
        return response($data);
    }

    public function getPlanModules(Request $request, $id)
    {
        $mainmodules = MainModule::leftJoin('plan_has_modules', 'main_modules.id', 'plan_has_modules.module_id')->select('main_modules.id as main_module_id', 'main_modules.name', 'main_modules.field', 'plan_has_modules.plan_id', 'plan_has_modules.module_id', 'plan_has_modules.enabled')->where('plan_has_modules.plan_id', $request->plan_id)->get();
        $data = ['status' => true, 'message' => 'Plans Modules fetched Successfully', 'data' => $mainmodules];
        return response($data);
    }

    public function changePlan(Request $request, $id)
    {
        $modules = MainModule::leftJoin('plan_has_modules', 'main_modules.id', 'plan_has_modules.module_id')->select('main_modules.id as main_module_id', 'main_modules.name', 'main_modules.field', 'plan_has_modules.plan_id', 'plan_has_modules.module_id', 'plan_has_modules.enabled')->where('plan_has_modules.plan_id', $request->plan_id)->get();
        $clientSetting = ClientSetting::where('company_id', $id)->first();
        if ($clientSetting) {
            foreach ($modules as $module) {
                $field = $module->field;
                if ($module->enabled == 1) {
                    $clientSetting->$field = 1;
                    if ($field == 'retailer_app') {
                        $role = Role::whereCompanyId($id)->whereName('Full Access')->whereGuardName('web')->first();
                        if ($role) {
                            $role_id = $role->id;
                            $permission_category = DB::table('permission_categories')->whereName('outlet')->whereDisplayName('Outlet Connection')->first();
                            if ($permission_category) {
                                $permission_category_id = $permission_category->id;
                                $permissions = DB::table('permissions')->wherePermissionCategoryId($permission_category_id)->pluck('id')->toArray();
                                if (!empty($permissions)) {
                                    $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                                    $non_exist_role_permission = array_merge($permissions, $exist_role_permissions);
                                    if (!empty($non_exist_role_permission)) {
                                        $role->syncPermissions($non_exist_role_permission);
                                    }
                                }
                            }
                        }

                    }

                    if ($field == 'visit_module') {
                        $role = Role::whereCompanyId($id)->whereName('Full Access')->whereGuardName('web')->first();
                        $permission_category_visit_module = DB::table('permission_categories')->where('name', 'LIKE', '%Party_Visit%')->first();
                        if ($permission_category_visit_module) {
                            $permission_category_visit_module_id = $permission_category_visit_module->id;
                            $permissions_visit_module = DB::table('permissions')->wherePermissionCategoryId($permission_category_visit_module_id)->pluck('name')->toArray();
                            foreach ($permissions_visit_module as $permission_name) {
                                $role->givePermissionTo($permission_name);
                            }
                        }
                    }

                    if ($field == 'custom_module') {
                        $role = Role::whereCompanyId($id)->whereName('Full Access')->whereGuardName('web')->first();
                        $role->givePermissionTo('Custom-Module-view');
                    }

                    if ($field == 'party_files' || $field == 'party_images') {
                        $permission_category_name = $field == 'party_images' ? 'image_uploads' : 'file_uploads';
                        $this->enableForFullAccess($id, $field, $permission_category_name);
                    }

                } else {
                    $clientSetting->$field = 0;
                    if ($field == 'product' || $field == 'orders') {
                        $clientSetting->party_wise_rate_setup = 0;
                    }
                }
            }
            $clientSetting->save();
            $company_plan = DB::table('company_plan')->where('company_id', $id)->update(['plan_id' => $request->plan_id]);
            $fbIDs = DB::table('employees')->where(array(array('company_id', $id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($clientSetting), "action" => "update");
            $sent = sendPushNotification_($fbIDs, 12, null, $dataPayload);
            $data = ['status' => true, 'message' => 'Plan Changed Successfully'];
            return response($data);
        }
        $data = ['status' => false, 'message' => 'Plan Changing Failed'];
        return response($data);
    }

    public function updateLogsActivityCompanyId()
    {
        $allUsersActivity = array_unique(LogActivity::whereNotNull('causer_id')->pluck('causer_id')->toArray());
        $companyIds = array_unique(User::whereIn('id', $allUsersActivity)->whereNotNull('company_id')->pluck('company_id')->toArray());
        $companyInstances = Company::whereIn('id', $companyIds)->get(['id', 'company_name']);
        foreach ($companyInstances as $companyInstance) {
            $employeeIds = $companyInstance->users()->pluck('users.id')->toArray();
            LogActivity::whereIn('causer_id', $employeeIds)->update([
                'company_id' => $companyInstance->id
            ]);
        }
    }

    public function companyusagenew()
    {
        $companies_count = Company::orderby('id', 'desc')->count();
        return view('admin.companies.companyusage_new', compact('companies_count'));

    }

    public function fetchUsageCompany(Request $request)
    {
        $columns = array('id', 'company_name', 'num_active_users', 'last_activity_time', 'last_th_days', 'last_sv_days');

        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $search = $request->input('search.value');
        $filterDays = $request->get('filterDays');

        $today = date("Y-m-d");
        $dateThreeDaysBefore = date("Y-m-d", strtotime("-3 day"));
        $dateSevenDaysBefore = date("Y-m-d", strtotime("-7 day"));

        $totalData = Company::with('activeemployees')->get(['id', 'company_name'])->count();
        $getCompanies = Company::with('activeemployees');
        if ($search) {
            $getCompanies = $getCompanies->where(function ($query) use ($search) {
                $query->orWhere('companies.company_name', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = (clone $getCompanies)->count();
        $companies = $getCompanies->orderBy($order, $dir)->offset($start)
            ->limit($limit)->get(['id', 'company_name']);
        $company_ids = $companies->pluck('id')->toArray();
        $user_ids = User::whereIn('company_id', $company_ids)->pluck('id')->toArray();

        $allUsersActivity = LogActivity::whereIn('causer_id', $user_ids)->orderBy('created_at', 'desc')->get(['activity_log.id', 'activity_log.causer_id', 'activity_log.company_id', 'activity_log.created_at as created_datetime', DB::raw('CAST(created_at AS DATE) as created_at')]);
        $data = array();
        if (!empty($companies)) {
            $i = $start;
            foreach ($companies as $company) {
                $id = $company->id;
                $company_user_id = User::where('company_id', $id)->pluck('id')->toArray();
                if ($company->activitylogged->first()) {
                    $no_of_active_users = $company->activeemployees->count();
                    $last_activity_instance = $allUsersActivity->whereIn('causer_id', $company_user_id)->sortByDesc('id')->first();
                    if ($last_activity_instance) $last_activity_time = date("Y-m-d h:i A", strtotime($last_activity_instance->created_datetime));
                    else $last_activity_time = null;

                    $no_of_active_in_th_days = $allUsersActivity->whereIn('causer_id', $company_user_id)->where('created_at', '>=', $dateThreeDaysBefore)->pluck('causer_id')->toArray();
                    $no_of_active_in_sv_days = $allUsersActivity->whereIn('causer_id', $company_user_id)->where('created_at', '>=', $dateSevenDaysBefore)->pluck('causer_id')->toArray();

                    $no_of_active_in_th_days = count(array_unique($no_of_active_in_th_days));
                    $no_of_active_in_sv_days = count(array_unique($no_of_active_in_sv_days));
                } else {
                    $no_of_active_users = $company->activeemployees->count();
                    $last_activity_time = null;
                    $no_of_active_in_th_days = 0;
                    $no_of_active_in_sv_days = 0;
                }
                $nestedData['id'] = ++$i;
                $nestedData['company_name'] = $company->company_name;
                $nestedData['last_activity_time'] = $last_activity_time;
                $nestedData['num_active_users'] = $no_of_active_users;
                $nestedData['last_th_days'] = $no_of_active_in_th_days;
                $nestedData['last_sv_days'] = $no_of_active_in_sv_days;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
            "total" => null
        );

        return json_encode($json_data);
    }


    public function companyusage(Request $request)
    {
        // $this->updateLogsActivityCompanyId();
        set_time_limit(500);
        $companies = Company::with('activeemployees')->orderby('id', 'desc')->get(['id', 'company_name']);
        $today = date("Y-m-d");
        $dateThreeDaysBefore = date("Y-m-d", strtotime("-3 day"));
        $dateSevenDaysBefore = date("Y-m-d", strtotime("-7 day"));

        $allUsersActivity = LogActivity::orderBy('created_at', 'desc')->get(['activity_log.id', 'activity_log.causer_id', 'activity_log.company_id', 'activity_log.created_at as created_datetime', DB::raw('CAST(created_at AS DATE) as created_at')]);

        foreach ($companies as $company) {
            if ($company->activitylogged->first()) {
                $no_of_active_users = $company->activeemployees->count();
                $last_activity_instance = $allUsersActivity->where('company_id', $company->id)->sortByDesc('created_datetime')->first();
                if ($last_activity_instance) $last_activity_time = date("Y-m-d h:i A", strtotime($last_activity_instance->created_datetime));
                else $last_activity_time = null;

                $no_of_active_in_sv_days = (clone $allUsersActivity)->where('company_id', $company->id)->where('created_at', '>=', $dateSevenDaysBefore)->groupBy('causer_id')->toArray();
                $no_of_active_in_th_days = (clone $allUsersActivity)->where('company_id', $company->id)->where('created_at', '>=', $dateThreeDaysBefore)->groupBy('causer_id')->toArray();

                $company->active_users = $no_of_active_users;
                $company->last_activity_time = $last_activity_time;
                $company->no_of_active_in_th_days = count($no_of_active_in_th_days);
                $company->no_of_active_in_sv_days = count($no_of_active_in_sv_days);
            } else {
                $company->active_users = $company->activeemployees->count();
                $company->last_activity_time = null;
                $company->no_of_active_in_th_days = 0;
                $company->no_of_active_in_sv_days = 0;
            }

        }
        return view('admin.companies.companyusage', compact('companies'));
    }

    // private function addPermission($company_id,$partytype,$permissionCategory,$permissionTag)
    // {
    //     $stringName                         = str_replace(' ','-',$partytype->name);
    //     $permission                         = new Permission;
    //     $permission->permission_category_id = $permissionCategory->id;
    //     $permission->company_id             = $company_id;
    //     $permission->name                   = $stringName.'-'.$permissionTag;
    //     $permission->guard_name             = 'web';
    //     $permission->permission_type        = 'Company';
    //     $permission->enabled                = 1;
    //     $permission->is_mobile              = 1;
    //     $permission->save();

    //     return $permission->id;
    // }

    private function addPermission($company_id, $partytype, $permissionCategory, $permissionTag)
    {
        $stringName = str_replace(' ', '-', $partytype->name);
        $existsPermission = Permission::whereCompanyId($company_id)->wherePermissionCategoryId($permissionCategory->id)->whereName($stringName . '-' . $permissionTag)->whereGuardName('web')->wherePermissionType('Company')->first();
        if (!$existsPermission) {
            $permission = new Permission;
            $permission->permission_category_id = $permissionCategory->id;
            $permission->company_id = $company_id;
            $permission->name = $stringName . '-' . $permissionTag;
            $permission->guard_name = 'web';
            $permission->permission_type = 'Company';
            $permission->enabled = 1;
            $permission->is_mobile = 1;
            $permission->save();
            return $permission->id;
        } else {
            return $existsPermission->id;
        }
    }


    private function updatePermission($company_id, $partytype, $oldPartyType, $permissionCategory, $permissionTag)
    {
        $stringName = str_replace(' ', '-', $oldPartyType->name) . '-' . $permissionTag;
        $permission = Permission::where('company_id', $company_id)->where('permission_category_id', $permissionCategory->id)
            ->where('name', $stringName)->first();
        $permission->name = str_replace(' ', '-', $partytype->name) . '-' . $permissionTag;
        $permission->save();
    }

    private function destroyPermission($company_id, $permissionCategory, $permissionTag)
    {
        $permission = Permission::where('company_id', $company_id)->where('permission_category_id', $permissionCategory->id)
            ->first();
        DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
        $permission->delete();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }



// private function syncOlderPartyTypeRolePermission($companyId){
// $partytypes = PartyType::where('company_id', $companyId)->get();
// foreach ($partytypes as $partytype) {
// try{
// DB::beginTransaction();
// $company_id = $partytype->company_id;
// $permissionCategory = new PermissionCategory;
// $permissionCategory->company_id = $company_id;
// $permissionCategory->permission_model_id = $partytype->id;
// $permissionCategory->permission_model = 'PartyType';
// $permissionCategory->permission_category_type = 'Company';
// $permissionCategory->name = str_replace(' ', '_', $partytype->name);
// $permissionCategory->display_name = 'Party Type: '.$partytype->name;
// $permissionCategory->indexing_priority = 10;
// $permissionCategory->save();
// $create_id = $this->addPermission($company_id,$partytype, $permissionCategory, 'create');
// $view_id = $this->addPermission($company_id,$partytype, $permissionCategory, 'view');
// $update_id = $this->addPermission($company_id,$partytype, $permissionCategory, 'update');
// $delete_id = $this->addPermission($company_id,$partytype, $permissionCategory, 'delete');
// $status_id = $this->addPermission($company_id,$partytype, $permissionCategory, 'status');
// DB::Commit();
// // $party_type_permissions = array($create_id, $view_id, $update_id, $delete_id, $status_id);
// // $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
// // if ($role) {
// // $role_id = $role->id;
// // $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
// // $non_exist_role_permission = array_merge($party_type_permissions, $exist_role_permissions);
// // if (!empty($non_exist_role_permission)) {
// // $role->syncPermissions($non_exist_role_permission);
// // }
// // }
// }catch(\Exception $e){
// Log::info($e->getMessage());
// DB::rollback();
// }
// }
// }

    private function syncOlderPartyTypeRolePermission($companyId)
    {
        set_time_limit(300);
        $partytypes = PartyType::where('company_id', $companyId)->get();
        foreach ($partytypes as $partytype) {
            try {
                $company_id = $companyId;
                $exist_permission = PermissionCategory::whereCompanyId($company_id)->wherePermissionModelId($partytype->id)->wherePermissionModel('PartyType')->wherePermissionCategoryType('Company')->whereName(str_replace(' ', '_', $partytype->name))->whereDisplayName('Party Type: ' . $partytype->name)->first();
                dump(!$exist_permission);
                if (!$exist_permission) {
                    DB::beginTransaction();
                    $permissionCategory = new PermissionCategory;
                    $permissionCategory->company_id = $company_id;
                    $permissionCategory->permission_model_id = $partytype->id;
                    $permissionCategory->permission_model = 'PartyType';
                    $permissionCategory->permission_category_type = 'Company';
                    $permissionCategory->name = str_replace(' ', '_', $partytype->name);
                    $permissionCategory->display_name = 'Party Type: ' . $partytype->name;
                    $permissionCategory->indexing_priority = 10;
                    $permissionCategory->save();
                    $create_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'create');
                    $view_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'view');
                    $update_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'update');
                    $delete_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'delete');
                    $status_id = $this->addPermission($company_id, $partytype, $permissionCategory, 'status');

                    DB::Commit();

                    $party_type_permissions = array($create_id, $view_id, $update_id, $delete_id, $status_id);
                    $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
                    if ($role) {
                        $role_id = $role->id;
                        $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
                        $non_exist_role_permission = array_merge($party_type_permissions, $exist_role_permissions);
                        if (!empty($non_exist_role_permission)) {
                            $role->syncPermissions($non_exist_role_permission);
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::info($e->getMessage());
                DB::rollback();
            }
        }
    }


}
