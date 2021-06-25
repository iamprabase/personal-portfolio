<?php
 
namespace App\Http\Controllers\Company\Admin;
use Auth;
use Carbon\Carbon;
use Log;
use DB;
use App\User;
use Mail;
use App\Activity;
use App\Beat;
use App\Brand;
use App\Category;
use App\UnitTypes;
use App\Order;
use App\NoOrder; 
use App\Holiday;
use App\Client;
use App\Product;
use App\Employee;
use App\Designation;
use App\PartyType;
use App\BeatVPlan;
use App\Attendance;
use App\Collection;
use App\ClientSetting;
use App\ClientVisit;
use App\Leave;
use App\Location;
use App\TourPlan;
use App\Expense;
use App\AnalyticsSave;
use App\ExpenseType;
use App\Outlet;
use App\TargetSalesmanassign;
use App\Services\NepaliConverter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Note;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NepaliConverter $neDate)
    {
        $this->middleware('auth');
        $this->neDate = $neDate;
    }

    private function sendResponse($result, $message){
      $response = [
          'success' => true,
          'data' => $result,
          'message' => $message
      ];
      return response()->json($response, 200);
    }

    private function sendError($error, $errorMessage = []){
      $response = [
          'success' => false,
          'message' => $error
      ];
      $response['data'] = (!empty($errorMessage)) ? $errorMessage : "";
      return response()->json($response, 404);
    }

    private function logicLayer($new_data){
      $arr = [];
      foreach ($new_data as $new) {
          foreach ($new as $k => $v) {
              if (isset($arr[$k])) {
                  $arr[$k] += $v;
              } else {
                  $arr[$k] = $v;
              }
          }
      }
      $dates = array_keys($arr);
      $values = array_values($arr);
      $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
      $result_array = array(
          'dates' => $dates,
          'values' => $values,
          'currency_symbol' => $currency
      );
      return $result_array;
    }

    public function assignRoleToUser(Request $request){
      $role = Role::whereCompanyId($request->company_id)->where('id', $request->role_id)->first();
      User::find($request->user_id)->assignRole($role->name);
      dump("Attached");
    }

    public function home_orig(Request $request, $domain)
    {   
        $collectionamt=[];
        $orderamt='';
        $monthName[]='';
        $company_id=config('settings.company_id');
        $employee_breakdowns = Auth::user()->handleQuery('employee')->leftJoin('designations', 'designations.id', 'employees.designation')->where('employees.status', 'Active')->whereNotNull('employees.designation')->groupby('employees.designation')->get(['designations.name as designation', 'employees.designation as id', DB::raw("COUNT(employees.designation) as count")]);
        $employees_count = Auth::user()->handleQuery('employee')->where('employees.status', 'Active')->get()->count();
        $partiescount = Auth::user()->handleQuery('client')->where('clients.status', 'Active')->get()->count();

        $types=Auth::user()->handleQuery('client')
            ->join('partytypes', 'partytypes.id', '=', 'clients.client_type')
            ->where('clients.status', 'Active')
            ->groupBy('client_type')
            ->get(['partytypes.id as ptypeid','clients.client_type','partytypes.name', DB::raw('count(*) as count')]);
        // echo "<pre>";
        //  print_r($types);
        $label_type = ['label-danger', 'label-warning', 'label-info', 'label-success', 'label-secondary', 'label-primary', 'label-default'];
        foreach($types as $partytype){
            $stringName = str_replace(' ','-',$partytype['name']).'-view';
            $permission = Permission::where('company_id',$company_id)->where('name',$stringName)->first();
            if($permission){
                if(Auth::user()->hasPermissionTo($permission->id)){
                    $partytype->can = true;
                }else{
                    $partytype->can = false;
                }
            }else{
                $partytype->can = false;
            }
        }
        $label_type = ['label-danger', 'label-warning', 'label-info', 'label-success', 'label-secondary', 'label-primary', 'label-default'];

        $prodcount = Product::where('company_id', $company_id)->where('status', 'Active')->get()->count();


        if (config('settings.company_id') == 55) {
            $recentorders = Order::select('clients.id as client_id', 'clients.company_name', 'clients.superior', 'clients.client_type', 'orders.id', 'orders.order_no', 'orders.order_date', 'employees.name as salesman', 'employees.id as employee_id','orders.grand_total', 'outlets.contact_person')
                ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('employees', 'orders.employee_id', '=', 'employees.id')
                ->leftJoin('outlets', 'orders.outlet_id', '=', 'outlets.id')
                ->where('orders.company_id', $company_id)
                ->where('clients.client_type', 1)
                // ->whereNull('clients.superior')
                ->limit(10)->orderBy('order_date', 'desc')->orderBy('id', 'desc')
                ->get()->toArray();
        } elseif (config('settings.company_id') == 56) {
            $recentorders = Order::select('clients.id as client_id', 'clients.company_name', 'clients.superior', 'clients.client_type', 'orders.id', 'orders.order_no', 'orders.order_date', 'employees.name as salesman', 'employees.id as employee_id','orders.grand_total', 'outlets.contact_person')
                ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('employees', 'orders.employee_id', '=', 'employees.id')
                ->leftJoin('outlets', 'orders.outlet_id', '=', 'outlets.id')
                ->where('orders.company_id', $company_id)
                ->where('clients.client_type',8)
                // ->whereNull('clients.superior')
                ->limit(10)->orderBy('order_date', 'desc')->orderBy('id', 'desc')
                ->get()->toArray();
        } else {
            if(Auth::user()->employee->is_admin==1){
                $query = Auth::user()->handleQuery('order');
            }else{
                $allOrders = Auth::user()->handleQuery('order')->get();
                $employeeHandledOrders = $allOrders
                    ->pluck('id')
                    ->toArray();
                $client_handles = Auth::user()->handleQuery('client')->pluck('id')->toArray();
                $retailerOrders = Order::whereCompanyId($company_id)->whereIn('client_id', $client_handles)->whereEmployeeId(0)->whereNotNull('outlet_id')->pluck('id')->toArray();
                if(!empty($retailerOrders)){
                    foreach($retailerOrders as $retailerOrder){
                        array_push($employeeHandledOrders, $retailerOrder);
                    }
                }
                $query = Order::whereIn('orders.id', $employeeHandledOrders);
            }
            $recentorders = $query
                ->leftJoin('outlets', 'orders.outlet_id', '=', 'outlets.id')->limit(10)->orderBy('order_date', 'desc')->orderBy('orders.id', 'desc')->get(['orders.*', 'outlets.contact_person'])->toArray();
        }

        $recentcollections = Auth::user()->handleQuery('collection')->limit(10)->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->get();

        $orders = Auth::user()->handleQuery('order')
            ->select(DB::raw('SUM(tot_amount) as orderamts, MONTH(order_date) as month, YEAR(order_date) as year'))
            ->groupBy(DB::raw('YEAR(order_date), MONTH(order_date)'))->orderBy('year', 'ASC')->orderBy('month', 'ASC')->get();

        $monthName = array();
        if(!empty($orders)){
            foreach ($orders as $order) {
                $monthName[] = date("M", mktime(0, 0, 0, $order->month, 10));
                $orderamt[$order->month] = isset($order->orderamts)?$order->orderamts:"0.00";
            }
        }
        // die;

        $collections = Auth::user()->handleQuery('collection')
            ->select(DB::raw('SUM(payment_received) as collectionamts, MONTH(payment_date) as month, YEAR(payment_date) as year'))
            ->groupBy(DB::raw('YEAR(payment_date), MONTH(payment_date)'))->orderBy('year', 'ASC')->orderBy('month', 'ASC')->get();

        foreach ($collections as $collection) {
            $collectionamt[$collection->month] = $collection->collectionamts;
        }

        $today = date('Y-m-d');

        $attendances = Auth::user()->handleQuery('attendance')->select('employee_id')
            ->where('adate', '=', $today)->where('check_type', 1)->distinct('employee_id')->get()->toArray();
        if(empty($attendances)){
            $attendance['present_employees'] = 0;
            $attendance['absent_employees'] = 0;
        }else{
            $attendance['present_employees'] = sizeof($attendances);
            $attendance['absent_employees'] = NULL;
        }

        $alert_msg = ""; $disabled_msg = "";
        if (getCompanyStatus($company_id)){
            $disabled_msg = "<p><b>Your subscription has ended. Please contact us to enable your account.</b></p>";

        }elseif(!getCompanyStatus($company_id)){
            if(url()->previous()=="https://".request()->getHttpHost()."/login"){
                if(Auth::user()->managers->first()){
                    if(getCompanySubscriptionDate($company_id) && Auth::user()->managers->first()->is_owner==1){
                        $alert_msg = "<b>Your subscription has ended. Please contact us to make payment.<b>";
                    }
                }
            }
        }

        return view('company.admin.dashboard', compact('alert_msg', 'disabled_msg', 'employee_breakdowns', 'employees_count', 'partiescount', 'prodcount', 'orderamt', 'collectionamt', 'recentorders', 'recentcollections', 'monthName', 'types', 'label_type', 'attendance'));
    }

    public function getpath(){
        // $allclients= Auth::user()->handleQuery('client')->where('clients.status', 'Active')->whereNotNull('latitude')->whereNotNull('longitude')->pluck('company_name','id');
        // dd($allclients);
        $company_id=config('settings.company_id');
        $allbeats= Beat::where('company_id',$company_id)->where('beats.status', 'Active')->pluck('name','id');
        return view('company.admin.path', compact('allbeats'));

    }

    public function getPartiesFromBeat(Request $request){

        $partiesbeats = DB::table('beat_client')->where('beat_id', $request->beat)->pluck('client_id')->toArray();



        //return $partiesbeats;

        $allclients = Client::select('company_name','id')->whereIn('id',$partiesbeats)->where('status','Active')->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('company_name','asc')->get()->toJson();
        return $allclients;
    }

    public function getclientspath(Request $request){

        $toclient=Client::select('company_name','latitude','longitude')->whereIn('id',$request->toclient)->whereNotNull('latitude')->whereNotNull('longitude')->get();

        $result['toclient']=$toclient;

        return $result;

        //return json_encode($result);

    }

    public function mail(Request $request)
    {
        $customMessages = [
            'email.required' => 'E-mail is a required field.',

        ];

        $this->validate($request, [
            'email' => 'required|email',

        ], $customMessages);

        $report_mail = array(
            'email' => $request->input('email'),
            'link' => $request->input('mapurl'),
        );
        // $company_id = config('settings.company_id');
        $company_name = config('settings.title');

        $emails = $request->input('email');
        $subject = 'Route Map';
        $url = $request->input('mapurl');
        Mail::send('company.routemap', ['url' => $url, 'company_name' => ''], function ($message) use ($report_mail, $subject) {
            $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
            $message->to($report_mail['email']);
            $message->subject($subject);
        });

        return response()->json(
            ['message' => 'Mail is Sent.',
                'url' => domain_route('company.admin.getpath'),
            ], 200);
    }
    public function homeSearch(Request $request){
        $query = $request->get('query');
        $getCompanyId = Auth::user()->company_id;
        if($request->ajax()){ 
            if($query !=''){
                $total_emp = null;
                $total_party = null;
                $total_product = null;
                $total_order = null;
                $noViewIcon= null;
                $product_admin_view = null;
                $order_admin_view = null;
                $parties_admin_view = null;
                $orderDatas = null;
                $filter_query = null;
                $order_test=null;
                $emp_user_roles = null;
                $parties_user_roles = null;
                $product_user_roles = null;
                $order_user_roles = null;
                $output = '<ul class="nav nav-tabs" id="search-list-ul"">';
                if(Auth::user()->can('employee-view')) {
                    $empDatas = Auth::user()->handleQuery('employee')->select('name', 'id', 'employee_code')
                        ->where(function ($q) use ($query) {
//                            $q->orWhereNotNull('deleted_at');
                            $q->orWhere('name', 'LIKE', "%{$query}%");
                            $q->orWhere('employee_code', 'LIKE', "%{$query}%");
//                            $q->orderBy('id', 'desc');
                        })->get();
                    $total_emp = $empDatas->count();
                    $emp_tab_content = '( '.$total_emp.' )';
                }else{
                    $emp_tab_content = null;

                    $emp_user_roles = 'notView';

                }
                $output .= '<li class="active" id="emp"><a href="#home"><i class="fa fa-user"></i> '.$emp_tab_content.'</a></li>';
                //Party data search on the base of name and code/short name
                if(config('settings.party')==1) {
                    if (Auth::user()->can('party-view')) {
                        $partytypes = PartyType::whereCompanyId($getCompanyId)->pluck('name', 'id')->toArray();
                        $accessible_party_types = array(0);
                        if (!empty($partytypes)) {
                            foreach ($partytypes as $id => $name) {
                                $stringName = str_replace(" ", "-", $name) . "-view";
                                $permission_id = DB::table('permissions')->where('name', 'LIKE', $stringName)
                                ->whereCompanyId($getCompanyId)->first();
                                if($permission_id){
                                  if (Auth::user()->hasPermissionTo($permission_id->id)) {
                                      array_push($accessible_party_types, $id);
                                  }
                                }
                            }
                        }
                        $partyDatas = Auth::user()->handleQuery('client')->select('company_name', 'id', 'client_code')
                            ->where(function ($q) use ($query) {
                                $q->orWhere('company_name', 'LIKE', "%{$query}%");
                                $q->orWhere('client_code', 'LIKE', "%{$query}%");

                            })->whereIn('client_type', $accessible_party_types)->get();

                        $total_party = $partyDatas->count();
                        $party_tab_content = '(' . $total_party . ')';
                    } else {
                        $party_tab_content = null;
                        $parties_user_roles = 'notView';
                    }
                }else{
                    $party_tab_content = null;
                    $parties_admin_view = 'no';

                }
                $output .= '<li id="pa"><a href="#parties"><i class="fa fa-user-secret"></i> '.$party_tab_content.'</a></li>';
                //Product data search base on Product name
                if(config('settings.product')==1) {
                    if (Auth::user()->can('product-view')) {
                        $productDatas = Product::select('product_name', 'id')
                            ->Where('company_id', $getCompanyId)
                            ->Where('product_name', 'LIKE', "%{$query}%")
                            ->get();

                        $total_product = $productDatas->count();
                        $product_tab_content = '(' . $total_product . ')';
                    } else {
                        $product_tab_content = null;
                        $product_user_roles = 'notView';
                    }
                }else{
                    $product_tab_content = null;
                    $product_admin_view = 'no';
                }
                $output .= '<li id="pro"><a href="#products"><i class="fa fa-th-large"></i> '.$product_tab_content.'</a></li>';
                // Order data search base on order ID

                if(config('settings.orders')==1) {
                    $order_prefix = config('settings.order_prefix');
//                    $pattern = "/" . $order_prefix . "/i";
////                $filter_query = preg_filter($new_prefix,'',$query);
//                    $order_q = preg_match($pattern, $query);
                    if (Auth::user()->can('order-view')) {
//                        if($order_q==1){
                        if($order_prefix){
                            $orderDatas = Auth::user()->handleQuery('order')
                                ->select('orders.id', 'orders.order_no', 'client_settings.order_prefix')
                                ->leftJoin('client_settings', 'client_settings.company_id', 'orders.company_id')
                                ->where(function ($q) use ($query) {
                                    $q->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$query}%");
                                })->get();
                            $total_order = $orderDatas->count();
                            $order_tab_content = '(' . $total_order . ')';
                        }else{

                            $orderDatas = Auth::user()->handleQuery('order')->select('id', 'order_no')
                                ->where(function ($q) use ($query) {
                                    $q->Where('order_no', 'LIKE', "%{$query}%");
                                })->get();
                            $total_order = $orderDatas->count();
                            $order_tab_content = '(' . $total_order . ')';
                        }

//                            $total_order = $orderDatas->count();
//                            $order_tab_content = '(' . $total_order . ')';

                    } else {
                        $order_tab_content = null;
                        $order_user_roles = 'notView';
                    }
                }else{
                    $order_tab_content = null;
                    $order_admin_view = 'no';
                }
                $output .= '<li id="ord"><a href="#orders"><i class="fa fa-cart-plus"></i> '.$order_tab_content.'</a></li>';

                $output .= '<li class="search-close"><a href="#close"><i class="fa fa-close"></i> </a></li>';

                $output .= '</ul>';
                $output .= '<div class="tab-content">';
                $output .= '<div id="home" class="tab-pane fade in active">';
                $output .= '<ul style="list-style-type:none;max-height:300px;overflow-y:scroll;padding:0px;">';

                if($total_emp==0 && $total_emp!==null){
                    $output .= '<li><a href="#" style="color:red !important">No records found</a></li>';
                }elseif($total_emp>0){
                    foreach($empDatas as $row){
                        if($row->employee_code){
                            $output .= '<li><a href="/admin/employee/'.$row->id.'#details-tap"> '.$row->name.' <span style="color:grey;">( '.$row->employee_code.' )</span></a></li>';
                        }else{
                            $output .= '<li><a href="/admin/employee/'.$row->id.'#details-tap"> '.$row->name.'</span></a></li>';
                        }

                    }
                }else{
//                    $output .= '<li><a href="#" style="color:red !important">Sorry! You are not authorized to view employee details.</a></li>';
                    $noViewIcon = 'noViewEmp';
                }

                $output .= '</ul>';
                $output .= '</div>';
                $output .= '<div id="parties" class="tab-pane fade">';
                $output .= '<ul style="list-style-type:none;max-height:300px;overflow-y:scroll;padding:0px;">';

                if($total_party==0 && $total_party!==null){
                    $output .= '<li><a href="#" style="color:red !important">No records found</a></li>';
                }elseif($total_party>0){
                    foreach($partyDatas as $row){
                        if($row->client_code){
                            $output .= '<li><a href="/admin/client/'.$row->id.'">'.$row->company_name.' <span style="color:grey;">( '.$row->client_code.' )</span></a></li>';
                        }else{
                            $output .= '<li><a href="/admin/client/'.$row->id.'">'.$row->company_name.'</a></li>';
                        }

                    }
                }else{
//                    $output .= '<li><a href="#" style="color:red !important">Sorry! You are not authorized to view party details.</a></li>';
                    $noViewIcon = 'noViewParty';
                }


                $output .= '</ul>';
                $output .= '</div>';
                $output .= '<div id="products" class="tab-pane fade">';
                $output .= '<ul style="list-style-type:none;max-height:300px;overflow-y:scroll;padding:0px;">';

                if($total_product==0  && $total_product!==null){
                    $output .= '<li><a href="#" style="color:red !important">No records found</a></li>';
                }elseif($total_product>0){
                    foreach($productDatas as $row){
                        $output .= '<li><a href="/admin/product/'.$row->id.'">'.$row->product_name.'</a></li>';
                    }
                }else{
//                    $output .= '<li><a href="#" style="color:red !important">Sorry! You are not authorized to view product details.</a></li>';
                    $noViewIcon = 'noViewProduct';
                }

                $output .= '</ul>';
                $output .= '</div>';
                $output .= '<div id="orders" class="tab-pane fade">';
                $output .= '<ul style="list-style-type:none;max-height:300px;overflow-y:scroll;padding:0px;">';
                if($total_order==0 && $total_order!==null){
                    $output .= '<li><a href="#" style="color:red !important">No records found</a></li>';
                }elseif($total_order>0){
                    $order_prefix = config('settings.order_prefix');
                    foreach($orderDatas as $row){
                        $output .= '<li><a href="/admin/order/'.$row->id.'">'.$order_prefix.$row->order_no.'</a></li>';
                    }
                }else{
//                    $output .= '<li><a href="#" style="color:red !important">Sorry! You are not authorized to view order details.</a></li>';
                    $noViewIcon = 'noViewOrder';
                }

                $output .= '</ul>';
                $output .= '</div>';
                $output .= '</div>';

                $data = array(
                    'emp_data'  => $output,
                    'view_test' => $noViewIcon,
                    'view_product' => $product_admin_view,
                    'view_order' =>  $order_admin_view,
                    'view_parties' => $parties_admin_view,
                    'test' => $order_test,
                    'iconUserEmp' => $emp_user_roles,
                    'iconUserPro' => $product_user_roles,
                    'iconUserParties' => $parties_user_roles,
                    'iconUserOrder' => $order_user_roles,
                    'accessible_party_types' => $accessible_party_types

                );

                echo json_encode($data);

            }else{
                echo 'Empty Fill';
                die;
            }
        }
    }

 

    public function home(Request $request){ 
      return view('company.admin.dashboard2');
    }


    public function homemod(Request $request){
      $company_id = config('settings.company_id');
      if(isset($request->startdate)){
        $startdate = $request->startdate;
        $startdate .= ' 00:00:00';
      }else{
        $startdate = Carbon::now()->toDateTimeString();
      }
      if(isset($request->enddate)){
        $enddate = $request->enddate;
        $enddate .= ' 00:00:00';
      }else{
        $enddate = Carbon::now()->toDateTimeString();
      }
      $todaydate = date('Y-m-d');
      $yesterdaydate = date('Y-m-d',strtotime("-1 days"));


      $data = array();
      $data['total_ordersdata'] = Order::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get();
      $data['total_orders'] = $data['total_ordersdata']->count();
      $data['no_zero_orders'] = NoOrder::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['total_ordersdata'] = $data['total_orders']+$data['no_zero_orders'];
      $data['total_comporders'] = Order::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->where('delivery_status','Approved')->get()->count();
      $data['new_parties'] = Client::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['total_collection'] = Collection::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['leaves'] = Leave::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['expenses'] = Expense::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['tours'] = TourPlan::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['companytargets'] = TargetSalesmanassign::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->groupby('target_groupid')->get()->count();
      $data['products_sold'] = Product::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $data['total_returns'] = DB::table('returns')->whereBetween('return_date',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $totalvisits = ClientVisit::with('client')->whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->get();
      $data['total_visitsdata'] = $totalvisits->take(10);
      $data['total_visits'] = $totalvisits->count();
      $data['chq_2deposit'] = Collection::whereBetween('created_at',[$startdate,$enddate])->where('payment_method','Cheque')->where('payment_status','Pending')->where('company_id',$company_id)->get()->count();
      $data['employee'] = Designation::with('employees')->whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->take(10)->get()->toArray();
      $data['parties'] = DB::select("select p.name,count(c.id) as clients FROM `partytypes` as p inner join clients as c on p.id=c.client_type where p.company_id=? and c.created_at between ? and ? group by p.name order by clients desc limit 0,10
      ", array($company_id,$startdate,$enddate));
      $data['products'] = Product::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
      $data['brands'] = Brand::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
      $data['categories'] = Category::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
      $data['units'] = UnitTypes::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();

      $data['latest_orders'] = Order::whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->take(10)->get()->toArray();
      $data['latest_collection'] = Collection::with('employees')->whereBetween('created_at',[$startdate,$enddate])->where('company_id',$company_id)->take(10)->get()->toArray();
      $data['recent_visits'] = $totalvisits->take(10)->toArray();
      $tot_time = 0;
      $data['tot_visittime'] = '';
      foreach($totalvisits->toArray() as $ret=>$ter){
        $stime = strtotime($ter['start_time']);
        $etime = strtotime($ter['end_time']);
        $tot_time += $etime-$stime;
      }
      if($tot_time>=0 && $tot_time<=60){
        $tot_visittime = $tot_time.' seconds ';
      }else if($tot_time>60 && $tot_time<=3600){
        $minutes = round((int)($tot_time/60),2).' minutes ';
        $second = round((int)($tot_time%60),2).' seconds';
        if($second==0){
        $tot_visittime = $minutes;
        }else{
        $tot_visittime = $minutes.$second;
        }
      }else{
        $hours = round((int)($tot_time/3600),2).' Hrs ';
        $minutes = round((int)($tot_time%60),2).' minutes';
        if($minutes==0){
            $tot_visittime = $hours;
        }else{
            $tot_visittime = $hours.$minutes;
        }
      }

      //graphs data
      $totorder_data = $zeroorder_data = $timeonvisit_data = $newparties_data = $expenses_data = $topparties_data = $topbeats_data = $totalcalls = $topproducts_data = $topbrands_data = array();
      $graph['totorder'] = DB::select("select created_at,count(id) FROM `orders` where company_id=? and created_at between ? and ? group by created_at order by created_at asc", array($company_id,$startdate,$enddate));
      foreach($graph['totorder'] as $ky=>$vl){
        $phpdate = strtotime($vl->created_at);
        $phpdates = date('Y-m-d',$phpdate);
        $totorder_data[$phpdates][] = $phpdates;
      }
      $graph['totorder'] = $totorder_data;
      $graph['zeroorder'] = DB::select("select created_at,count(id) FROM `no_orders` where company_id=? and created_at between ? and ? group by created_at order by created_at asc", array($company_id,$startdate,$enddate));
      foreach($graph['zeroorder'] as $ky=>$vl){
        $phpdate = strtotime($vl->created_at);
        $phpdates = date('Y-m-d',$phpdate);
        $zeroorder_data[$phpdates][] = $phpdates;
      }
      $graph['zeroorder'] = $zeroorder_data;
      $graph['timeonvisit'] = DB::select("select created_at,count(id) FROM `client_visits` where company_id=? and created_at between ? and ? group by created_at order by created_at asc", array($company_id,$startdate,$enddate));
      foreach($graph['timeonvisit'] as $ky=>$vl){
        $phpdate = strtotime($vl->created_at);
        $phpdates = date('Y-m-d',$phpdate);
        $timeonvisit_data[$phpdates][] = $phpdates;
      }
      $graph['timeonvisit'] = $timeonvisit_data;
      $graph['newpartiesadded'] = DB::select("select created_at,count(id) FROM `partytypes` where company_id=? and created_at between ? and ? group by created_at order by created_at asc", array($company_id,$startdate,$enddate));
      foreach($graph['newpartiesadded'] as $ky=>$vl){
        $phpdate = strtotime($vl->created_at);
        $phpdates = date('Y-m-d',$phpdate);
        $newparties_data[$phpdates][] = $phpdates;
      }
      $graph['newpartiesadded'] = $newparties_data;
      $graph['prodcalls'] = $graph['totorder'];
      $graph['expenses'] = DB::select("select created_at,count(id) FROM `expenses` where company_id=? and created_at between ? and ? group by created_at order by created_at asc limit 10", array($company_id,$startdate,$enddate));
      foreach($graph['expenses'] as $ky=>$vl){
        $phpdate = strtotime($vl->created_at);
        $phpdates = date('Y-m-d',$phpdate);
        $expenses_data[$phpdates][] = $phpdates;
      }
      $graph['expenses'] = $expenses_data;
      $graph['topparties'] = DB::select("select p.name,count(od.id) as orders FROM `partytypes` as p inner join clients as c on p.id=c.client_type inner join orders as od on od.client_id=c.id where p.company_id = ? and od.created_at between ? and ? group by p.parent_id order by orders desc", array($company_id,$startdate,$enddate));
      foreach($graph['topparties'] as $ky=>$vl){
       $topparties_data[$vl->name] = $vl->orders;
      }
      $graph['topparties'] = $topparties_data;
      $graph['topbeats'] = DB::select("select b.name,count(od.id) as orders FROM `beats` as b inner join beat_client as bc on b.id=bc.beat_id inner join orders as od on od.client_id=bc.client_id where b.company_id = ? and od.created_at between ? and ? group by b.name order by orders desc limit 0,10", array($company_id,$startdate,$enddate));
      foreach($graph['topbeats'] as $ky=>$vl){
       $topbeats_data[$vl->name] = $vl->orders;
      }
      $graph['topbeats'] = $topbeats_data;
      foreach($graph['totorder'] as $kl=>$lk){
        $totorder_count =  count($graph['totorder'][$kl]);
        if(array_key_exists($kl,$graph['zeroorder'])){
          $totorder_count += count($graph['zeroorder'][$kl]);
        }
        $totalcalls[$kl] = $totorder_count;
      }
      $graph['totalcalls'] = $totalcalls;
      $graph['topproducts'] = DB::select("select count(op.product_id) as totcount,p.product_name  FROM `orders` as od inner join orderproducts as op on od.id=op.order_id inner join products as p on op.product_id=p.id where od.company_id=? and od.created_at between ? and ?  group by op.product_id order by totcount desc limit 0,10 ", array($company_id,$startdate,$enddate));
      foreach($graph['topproducts'] as $ky=>$vl){
       $topproducts_data[$vl->product_name] = $vl->totcount;
      }
      $graph['topproducts'] = $topproducts_data;
      $graph['topbrands'] = DB::select("select count(op.brand) as totcount,b.name FROM `orders` as od inner join orderproducts as op on od.id=op.order_id inner join brands as b on op.brand=b.id where od.company_id=? and od.created_at between ? and ? group by op.brand order by totcount desc limit 0,10 ", array($company_id,$startdate,$enddate));
      foreach($graph['topbrands'] as $ky=>$vl){
       $topbrands_data[$vl->name] = $vl->totcount;
      }
      $graph['topbrands'] = $topbrands_data;
      
      return view('company.admin.dashboard2', compact('data','graph'));
    } 


    public function tickerCountDatas(Request $request){
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');
        if(empty($st_date)){
          $startDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();;
          $endDate = Carbon::now()->format('Y-m-d');
        }else{
          $startDate = $st_date;
          $endDate = $ed_date;
        }
        $startdate = $startDate;
        $enddate = $endDate;
        $data = array();
        $icons_array = array('totord'=>'fa fa-cart-arrow-down','newpart'=>'fa fa-user-plus','chq2dep'=>'fa fa-cc','nozoord'=>'fa fa-shopping-cart','prodsold'=>'fa fa-shopping-basket','effoutl'=>'fa fa-volume-control-phone','totvstm'=>'fa fa-clock-o','totordat'=>'fa fa-cart-arrow-down','ordval'=>'fa fa-cart-plus','totcoll'=>'fa fa-money','totvis'=>'fa fa-thumb-tack');
        $ticker1_data = $this->tickerdropdowns('changeTicker1',$startdate,$enddate);
        $ticker2_data = $this->tickerdropdowns('changeTicker2',$startdate,$enddate);
        $ticker3_data = $this->tickerdropdowns('changeTicker3',$startdate,$enddate);
        $ticker4_data = $this->tickerdropdowns('changeTicker4',$startdate,$enddate);
        $icon1 = $icon2 = $icon3 = $icon4 = '';
        $title_data1 = $title_data2 = $title_data3 = $title_data4 = '';
        $title_val1 = $title_val2 = $title_val3 = $title_val4 = '';
        foreach($ticker1_data['nameval'] as $yuy=>$uuy){
          if(count($uuy)>=4){
            $title_data1 = $ticker1_data['nameval']['name'][0];
            $title_val1 = $ticker1_data['nameval']['val'][0];
            $icon1 = $icons_array[$ticker1_data['nameval']['icon'][0]];
            $title_data2 = $ticker1_data['nameval']['name'][1];
            $title_val2 = $ticker1_data['nameval']['val'][1];
            $icon2 = $icons_array[$ticker1_data['nameval']['icon'][1]];
            $title_data3 = $ticker1_data['nameval']['name'][2];
            $title_val3 = $ticker1_data['nameval']['val'][2];
            $icon3 = $icons_array[$ticker1_data['nameval']['icon'][2]];
            $title_data4 = $ticker1_data['nameval']['name'][3];
            $title_val4 = $ticker1_data['nameval']['val'][3];
            $icon4 = $icons_array[$ticker1_data['nameval']['icon'][3]];
          }else{
            $totaldata = count($uuy);
            switch($totaldata){
              case 3:
                $title_data1 = $ticker1_data['nameval']['name'][0];
                $title_val1 = $ticker1_data['nameval']['val'][0];
                $icon1 = $icons_array[$ticker1_data['nameval']['icon'][0]];
                $title_data2 = $ticker1_data['nameval']['name'][1];
                $title_val2 = $ticker1_data['nameval']['val'][1];
                $icon2 = $icons_array[$ticker1_data['nameval']['icon'][1]];
                $title_data3 = $ticker1_data['nameval']['name'][2];
                $title_val3 = $ticker1_data['nameval']['val'][2];
                $icon3 = $icons_array[$ticker1_data['nameval']['icon'][2]];
                $title_data4 = $ticker1_data['nameval']['name'][2];
                $title_val4 = $ticker1_data['nameval']['val'][2];
                $icon4 = $icons_array[$ticker1_data['nameval']['icon'][2]];
                break;
              case 2:
                $title_data1 = $ticker1_data['nameval']['name'][0];
                $title_val1 = $ticker1_data['nameval']['val'][0];
                $icon1 = $icons_array[$ticker1_data['nameval']['icon'][0]];
                $title_data2 = $ticker1_data['nameval']['name'][1];
                $title_val2 = $ticker1_data['nameval']['val'][1];
                $icon2 = $icons_array[$ticker1_data['nameval']['icon'][1]];
                $title_data3 = $ticker1_data['nameval']['name'][1];
                $title_val3 = $ticker1_data['nameval']['val'][1];
                $icon3 = $icons_array[$ticker1_data['nameval']['icon'][1]];
                $title_data4 = $ticker1_data['nameval']['name'][1];
                $title_val4 = $ticker1_data['nameval']['val'][1];
                $icon4 = $icons_array[$ticker1_data['nameval']['icon'][1]];
                break;
              case 1:
                $title_data1 = $ticker1_data['nameval']['name'][0];
                $title_val1 = $ticker1_data['nameval']['val'][0];
                $icon1 = $icons_array[$ticker1_data['nameval']['icon'][0]];
                $title_data2 = $ticker1_data['nameval']['name'][0];
                $title_val2 = $ticker1_data['nameval']['val'][0];
                $icon2 = $icons_array[$ticker1_data['nameval']['icon'][0]];
                $title_data3 = $ticker1_data['nameval']['name'][0];
                $title_val3 = $ticker1_data['nameval']['val'][0];
                $icon3 = $$icons_array[$ticker1_data['nameval']['icon'][0]];
                $title_data4 = $ticker1_data['nameval']['name'][0];
                $title_val4 = $ticker1_data['nameval']['val'][0];
                $icon4 = $icons_array[$ticker1_data['nameval']['icon'][0]];
                break;
            }
          }
        }

        $data['ticker1'] = '<div class="info-box">
                    <span class="info-box-icon bg-aqua elevation-1" id="ticker1_icon"><i class="'.$icon1.'"></i></span>
                    <div class="info-box-content">
                      <div class="info-box-content__wrapper">
                        <div class="info-box-text" id="col1name">'.$title_data1.'</div>
                        <div id="iicons_1"></div>
                        <div class="btn-group">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <span class="fa fa-chevron-down"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu">';
        $data['ticker1'] .= $ticker1_data['tickerdata'];
        $data['ticker1'] .= '</ul>
                        </div>
                      </div>
                      <span class="info-box-number" id="col1val">'.$title_val1.'</span>
                    </div>                    
                  </div>';
        
        $data['ticker2'] = '<div class="info-box">
                    <span class="info-box-icon bg-green elevation-1" id="ticker2_icon"><i class="'.$icon2.'"></i></span>
                    <div class="info-box-content">
                      <div class="info-box-content__wrapper">
                        <div class="info-box-text" id="col2name">'.$title_data2.'</div>
                        <div id="iicons_2"></div>
                        <div class="btn-group">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <span class="fa fa-chevron-down"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu">';
        $data['ticker2'] .= $ticker2_data['tickerdata'];
        $data['ticker2'] .= '</ul>                            
                                </ul>
                              </div>
                            </div>
                            <span class="info-box-number" id="col2val">'.$title_val2.'</span>
                          </div>                    
                        </div>';

        $data['ticker3'] = '<div class="info-box">
                        <span class="info-box-icon bg-maroon elevation-1" id="ticker3_icon"><i class="'.$icon3.'"></i></span>
                        <div class="info-box-content">
                          <div class="info-box-content__wrapper">
                            <div class="info-box-text" id="col3name">'.$title_data3.'</div>
                            <div id="iicons_3"></div>
                            <div class="btn-group">
                              <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="fa fa-chevron-down"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu">';
        $data['ticker3'] .= $ticker3_data['tickerdata'];
        $data['ticker3'] .= '</ul>                            
                                    </ul>
                                  </div>
                                </div>
                                <span class="info-box-number" id="col3val">'.$title_val3.'</span>
                              </div>                    
                            </div>';

        $data['ticker4'] = '<div class="info-box">
                            <span class="info-box-icon bg-teal elevation-1" id="ticker4_icon"><i class="'.$icon4.'"></i></span>
                            <div class="info-box-content">
                              <div class="info-box-content__wrapper">
                                <div class="info-box-text" id="col4name">'.$title_data4.'</div>
                                <div id="iicons_4"></div>
                                <div class="btn-group">
                                  <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <span class="fa fa-chevron-down"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu" style="left:unset;right:0">';
        $data['ticker4'] .= $ticker4_data['tickerdata'];
        $data['ticker4'] .= '</ul>  
                                </ul>
                              </div>
                            </div>
                            <span class="info-box-number" id="col4val">'.$title_val4.'</span>
                          </div>                    
                        </div>';

        return $this->sendResponse($data, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    private function tickerdropdowns($funcname,$startdate,$enddate){
      $company_id = config('settings.company_id');
      $total_orders = Order::whereBetween('order_date',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
      $no_zero_orders = NoOrder::whereBetween('date',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $std_d = $startdate.' 00:00:00';
      $etd_d = $enddate.' 23:59:59';
      $new_parties = Client::whereBetween('created_at',[$std_d,$etd_d])->where('company_id',$company_id)->get()->count();
      $chq_2deposit = Collection::whereBetween('payment_date',[$startdate,$enddate])->where('payment_method','Cheque')->where('payment_status','Pending')->where('company_id',$company_id)->get()->count();
      $chq_2deposit_cleared = Collection::whereBetween('payment_date',[$startdate,$enddate])->where('payment_method','Cheque')->where('payment_status','Cleared')->where('company_id',$company_id)->get()->count();
      $chq_2deposit_deposited = Collection::whereBetween('payment_date',[$startdate,$enddate])->where('payment_method','Cheque')->where('payment_status','Pending')->where('payment_status','Overdue')->where('company_id',$company_id)->get()->sum('payment_received');
      $total_ordersdata = $total_orders+$no_zero_orders;
      $products_sold = Order::select('products.product_name',DB::raw('count(orderproducts.id) as totcount'))
                                  ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                                  ->Join('products', 'orderproducts.product_id', 'products.id')
                                  ->where('orders.company_id', $company_id)
                                  ->whereBetween('orders.order_date',[$startdate,$enddate])
                                  ->groupby('orderproducts.product_id')
                                  ->get()->toArray();
      $productssold_count = 0;
      if(count($products_sold)>0){
        foreach($products_sold as $ps=>$sp){
          // $productssold_count += (int)$sp['totcount'];
          $productssold_count += 1;
        }
      }
      $products_sold = $productssold_count;
      $totalvisits = ClientVisit::with('client')->whereBetween('date',[$startdate,$enddate])->where('company_id',$company_id)->get();
      $total_visitsdata = $totalvisits->take(10);
      $total_visits = $totalvisits->count();
      $tot_visittime = '';$tot_time = 0;
      foreach($totalvisits->toArray() as $ret=>$ter){
        $stime = strtotime($ter['start_time']);
        $etime = strtotime($ter['end_time']);
        $tot_time += $etime-$stime;
      }
      if($tot_time>=0 && $tot_time<=60){
        $tot_visittime = $tot_time.' seconds ';
      }else if($tot_time>60 && $tot_time<=3600){
        $minutes = round((int)($tot_time/60),2).' minutes ';
        $second = round((int)($tot_time%60),2).' seconds';
        if($second==0){
        $tot_visittime = $minutes;
        }else{
        $tot_visittime = $minutes.$second;
        }
      }else{
        $hours = round((int)($tot_time/3600),2).' Hrs ';
        $minutes = round((int)($tot_time%60),2).' minutes';
        if($minutes==0){
            $tot_visittime = $hours;
        }else{
            $tot_visittime = $hours.$minutes;
        }
      }
      $total_comporders = Order::whereBetween('order_date',[$startdate,$enddate])->where('company_id',$company_id)->where('delivery_status','Approved')->get()->count();
      $total_returns = DB::table('returns')->whereBetween('return_date',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
      $total_collection = Collection::whereBetween('payment_date',[$startdate,$enddate])->where('company_id',$company_id)->where('payment_status','Cleared')->get()->sum('payment_received');
      $value_orders = Order::whereBetween('order_date',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->sum('grand_total');
      $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
      $totalcalls = $total_orders+$no_zero_orders;
      $effectivetotalcall_ratio = $total_orders.':'.$totalcalls;
      $ticker1 = ''; $tkr = $tempdata = array();
      if(config('settings.orders')==1){
          if(Auth::user()->can('order-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'totord\')" class="'.$funcname.'_totord" id="'.$total_orders.'">No. of Orders
            </a></li>';
            $tkr['name'][] = 'No. of Orders';
            $tkr['val'][] = $total_orders;
            $tkr['icon'][] = 'totord';
          }
      }
      if(config('settings.party')==1){
          if(Auth::user()->can('party-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'newpart\')" class="'.$funcname.'_newpart" id="'.$new_parties.'">New Parties Added</a></li>';
            $tkr['name'][] = 'New Parties Added';
            $tkr['val'][] = $new_parties;
            $tkr['icon'][] = 'newpart';
          }
      }

      // if(config('settings.collections')==1){
      //     if(Auth::user()->can('collection-view')){
      //       $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'chq2dep\')" class="'.$funcname.'_chq2dep" id="'.$chq_2deposit_deposited.'">Cheques to be Deposited</a></li>';
      //       $tkr['name'][] = 'Cheques to be Deposited';
      //       $tkr['val'][] = $chq_2deposit_deposited;
      //       $tkr['icon'][] = 'chq2dep';
      //     }
      // }

      if(config('settings.zero_orders')==1){
          if(Auth::user()->can('zeroorder-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'nozoord\')" class="'.$funcname.'_nozoord" id="'.$no_zero_orders.'">No. of Zero Orders</a></li>';
            $tkr['name'][] = 'No. of Zero Orders';
            $tkr['val'][] = $no_zero_orders;
            $tkr['icon'][] = 'nozoord';
          }
      }

      if(config('settings.product')==1 && config('settings.orders')==1){
          if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'prodsold\')" class="'.$funcname.'_prodsold" id="'.$products_sold.'">Products Sold</a></li>';
            $tkr['name'][] = 'Products Sold';
            $tkr['val'][] = $products_sold;
            $tkr['icon'][] = 'prodsold';
          }
      }

      if(config('settings.orders')==1 && config('settings.zero_orders')==1){
          if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'effoutl\')" class="'.$funcname.'_effoutl" id="'.$effectivetotalcall_ratio.'">Effective Calls:Total Calls Ratio</a></li>'; 
            $tkr['name'][] = 'Effective Calls:Total Calls Ratio';
            $tkr['val'][] = $effectivetotalcall_ratio;
            $tkr['icon'][] = 'effoutl';
          }
      }

      if(config('settings.visit_module')==1){
          if(Auth::user()->can('PartyVisit-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'totvstm\')" class="'.$funcname.'_totvstm" id="'.$tot_visittime.'">Time Spent on Visits</a></li>';
            $tkr['name'][] = 'Time Spent on Visits';
            $tkr['val'][] = $tot_visittime;
            $tkr['icon'][] = 'totvstm';
          }
      }

      if(config('settings.orders')==1 && config('settings.zero_orders')==1){
          if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'totordat\')" class="'.$funcname.'_totordat" id="'.$total_ordersdata.'">Total Calls</a></li>';
            $tkr['name'][] = 'Total Calls';
            $tkr['val'][] = $total_ordersdata;
            $tkr['icon'][] = 'totordat';
          }
      }

      if(config('settings.orders')==1){
          if(Auth::user()->can('order-view')){
            if(config('settings.order_with_amt')==0){
              $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'ordval\')" class="'.$funcname.'_ordval" id="'.$currency.' '.$value_orders .'">Order Value</a></li>';
              $tkr['name'][] = 'Order Value';
              $tkr['val'][] = $currency.' '.$value_orders;
              $tkr['icon'][] = 'ordval';
            }
          }
      }

      if(config('settings.collections')==1){
          if(Auth::user()->can('collection-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'totcoll\')" class="'.$funcname.'_totcoll" id="'.$currency.' '.$total_collection.'">Payment 
            Collection</a></li>';
            $tkr['name'][] = 'Payment Collection';
            $tkr['val'][] = $currency.' '.$total_collection;
            $tkr['icon'][] = 'totcoll';
          }
      }

      if(config('settings.visit_module')==1){
          if(Auth::user()->can('PartyVisit-view')){
            $ticker1 .= '<li><a onclick="'.$funcname.'(this,\'totvis\')" class="'.$funcname.'_totvis" id="'.$total_visits.'">No. of Visits</a></li>';
            $tkr['name'][] = 'No.of Visits';
            $tkr['val'][] = $total_visits;
            $tkr['icon'][] = 'totvis';
          }
      }

      $tempdata['tickerdata'] = $ticker1;
      $tempdata['nameval'] = $tkr;
      return $tempdata;
    }

    private function findactivities($empid){
      $company_id = config('settings.company_id');
      $activities =  Activity::select('activities.*','addedBy_tbl.id as AddedById','addedBy_tbl.name as AssignedByName','approvedBy_tbl.id as ApprovedById','approvedBy_tbl.name as AssignedToName','clients.id as ClientId','clients.company_name as PartyName','activity_types.id as type','activity_types.name as TypeName','activity_priorities.id as PriorityId','activity_priorities.name as PriorityName')
      ->leftJoin('employees as approvedBy_tbl','activities.assigned_to','approvedBy_tbl.id')
      ->leftJoin('employees as addedBy_tbl','activities.created_by','addedBy_tbl.id')
                ->leftJoin('clients','activities.client_id','clients.id')
                ->leftJoin('activity_types','activities.type','activity_types.id')
                ->leftJoin('activity_priorities','activities.priority','activity_priorities.id')
                ->where('activities.company_id',$company_id);
                // ->whereBetween('activities.start_datetime',[$startdate,$enddate]);
      // if(!(Auth::user()->isCompanyManager())){
        $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(),array());
        $activities = $activities->where(function($q)use($juniors,$empid){
          $q = $q->whereIn('activities.created_by',$juniors)->whereIn('activities.assigned_to',$juniors,'or')->orWhere('activities.created_by',$empid)->orWhere('activities.assigned_to',$empid);
        });
      // }
      return $activities;
    }

    public function infoBarsDatas(Request $request){
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');
        if(empty($st_date)){
          $startDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();;
          $endDate = Carbon::now()->format('Y-m-d');
        }else{
          $startDate = $st_date;
          $endDate = $ed_date;
        }
        $startdate = $startDate;
        $enddate = $endDate;
        $employee = Auth::user()->handleQuery('employee')->leftJoin('designations', 'designations.id', 'employees.designation')->where('employees.status', 'Active')->whereNotNull('employees.designation')->groupby('employees.designation')->get(['designations.name as designation', 'employees.designation as id', DB::raw("COUNT(employees.designation) as count")])->toArray();
        // $validclients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
        // $parties = PartyType::select('partytypes.id','partytypes.name',DB::raw('count(clients.id) as clients'))
        //                     ->leftJoin('clients','partytypes.id','clients.client_type')
                            // ->where(function($qry) use ($validclients)  {
                            //     $qry->orWhere('clients.status','Active');
                            //     $qry->orWhereNull('clients.status');
                            //     $qry->orWhereIn('clients.id',$validclients);
                            //  })
                            // ->where('partytypes.company_id',$company_id)
                            // ->whereNull('clients.deleted_at')
                            // ->groupby('partytypes.id')
                            // ->orderby('clients','desc')->get()->toArray();
                            
        if(Auth::user()->can('party-view') && config('settings.party')==1){                  
          $partytypes = PartyType::whereCompanyId($company_id)->pluck('name', 'id')->toArray();
          $accessible_party_types = array();
          foreach ($partytypes as $id => $name) {
            $stringName = str_replace(" ", "-", $name) . "-view";
            $permission_id = DB::table('permissions')->where('name', 'LIKE', $stringName)
            ->whereCompanyId($company_id)->first();
            if($permission_id){
              if (Auth::user()->hasPermissionTo($permission_id->id)) {
                  array_push($accessible_party_types, $id);
              }
            }
          }
 
          // $parties = Auth::user()->handleQuery('client')->select('partytypes.id','partytypes.name',DB::raw('count(clients.id) as clients'))           
          //                               ->rightJoin('partytypes','clients.client_type','partytypes.id')
          //                               ->where(function($qry) {
          //                                   $qry->orWhere('clients.status','Active');
          //                                 })
          //                                 ->whereIn('partytypes.id',$accessible_party_types)
          //                                 ->whereNull('clients.deleted_at')
          //                                 ->groupby('partytypes.id')
          //                                 ->orderby('partytypes.name','desc')->get()->toArray();  
          $accessible_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
          $parties = PartyType::select('partytypes.name', 'partytypes.id')->whereCompanyId($company_id)   ->whereIn('partytypes.id', $accessible_party_types)
          ->withCount(['clients' => function($query) use($accessible_clients){
            $query->whereIn('clients.id', $accessible_clients);
            $query->whereNull('clients.deleted_at');
            $query->where('clients.status','Active');
          }])->orderby('name','desc')->get()->toArray();
          $keys = array_column($parties, 'clients_count');
	        array_multisort($keys, SORT_DESC, $parties); 
        }else{
          $parties = array();
        }

        $parties_unspecified = Auth::user()->handleQuery('client')->where('company_id',$company_id)->where('status','Active')->where('client_type',0)->count();
        $products = Product::where('company_id',$company_id)->where('status','Active')->orderby('created_at','desc')->get()->count();
        $brands = Brand::where('company_id',$company_id)->where('status','Active')->orderby('created_at','desc')->get()->count();
        $categories = Category::where('company_id',$company_id)->where('status','Active')->orderby('created_at','desc')->get()->count();
        $units = UnitTypes::where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
        $latest_orders = Auth::user()->handleQuery('order')->with('clients')->with('employee')->where('company_id',$company_id)->orderby('created_at','desc')->take(20)->get()->toArray();
        $latest_collection = Auth::user()->handleQuery('collection')->with('client')->with('bank')->where('company_id',$company_id)->orderby('created_at','desc')->take(20)->get()->toArray();
        $recent_visits = Auth::user()->handleQuery('ClientVisit')->with('client')->with('employee')->where('company_id',$company_id)->orderby('created_at','desc')->take(20)->get()->toArray();
        $new_addedparties = Auth::user()->handleQuery('client')->select('clients.id','clients.name','clients.company_name','clients.created_at','employees.name as ename','employees.id as eid')
                              ->Join('employees','clients.created_by','employees.id')
                              ->where('clients.status','Active')
                              ->where('clients.company_id',$company_id)
                              ->orderby('created_at','desc')
                              ->take(20)->get()->toArray();
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $data = array(); 
        $html = '';$totemp = 0;
        if(Auth::user()->can('employee-view')){
          if(count($employee)>0){
            foreach($employee as $ky=>$vl){
              $totemp += $vl['count'];
              $html .= "<tr>";
              $html .= "<td>".$vl['designation']."</td>";
              $html .= "<td><span class='label label-success'>".$vl['count']."<span></td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='3'>No Data Available</td>";
              $html .= "</tr>";
          }
        }
        $data['employee'] = $html;
        $data['employee_count'] = $totemp;
        $html = '';$totparties = $parties_viewall_chk = $totparties_all = 0;
        if(Auth::user()->can('party-view')){
          if(count($parties)>0){
            $parties_viewall_chk = count($parties);
            foreach($parties as $ky=>$vl){
              $totparties += (int)$vl['clients_count'];
              $html .= "<tr>";
              $html .= "<td><a href='".domain_route('company.admin.client.subclients',[$vl['id']])."'>".$vl['name']."</a></td>";
              $html .= "<td><span class='label label-success'>".$vl['clients_count']."<span></td>";
              $html .= "</tr>";
            }
            if($parties_unspecified>0){
              $html .= "<tr>";
              $html .= "<td><a href='".domain_route('company.admin.client')."'>Unspecified</a></td>";
              $html .= "<td><span class='label label-success'>".$parties_unspecified."<span></td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='3'></td>";
              $html .= "</tr>";
          }
        }
        $data['parties'] = $html;
        $data['parties_viewall'] = $parties_viewall_chk;
        $data['parties_count'] = $totparties+$parties_unspecified;
        $html = '';
        if(Auth::user()->can('settings-view')){
          $html .= "<tr><td>Brands</td><td><span class='label label-success'>".$brands."</span></td></tr>";
          $html .= "<tr><td>Categories</td><td><span class='label label-success'>".$categories."<span></td></tr>";
        }
        $data['products_all'] = $html;
        if(Auth::user()->can('product-view')){
          $data['products_count'] = $products;
        }else{
          $data['products_count'] = '0';
        }
        $html = '';
        if(Auth::user()->can('order-view')){
          if(count($latest_orders)>0){
            foreach($latest_orders as $ky=>$vl){
              if(is_array($vl['clients'])){
                $cname = $vl['clients']['company_name'];
                $cid = $vl['clients']['id'];
              }else{
                $cname = '';
                $cid = '';
              }
              if(is_array($vl['employee'])){
                $ename = $vl['employee']['name'];
                $eid = $vl['employee']['id'];
              }else{
                $ename = '';
                $eid = '';
              }
              $html .= "<tr onclick='rowclicked(this)' class='clickable-row' id='".domain_route('company.admin.order.show',[$vl['id']])."'>";
              $d1 = domain_route('company.admin.order.show',[$vl['id']]);
              $html .= "<td width='10%'>".getClientSetting()->order_prefix.$vl['order_no']."</td>";
              $html .= "<td width='25%'>".$cname."</td>";
              $html .= "<td width='25%'>".getDeltaDate(Carbon::Parse($vl['created_at'])->format('Y-m-d'))."</td>";
              if(config('settings.order_with_amt')==0){
                $html .= "<td width='15%'>".$currency.' '.(number_format($vl['grand_total'], 2))."</td>";
              }
              $html .= "<td width='25%'>".$ename."</td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='3'>No Data Available</td>";
              $html .= "</tr>";
          }
        }
        $data['lateset_orders'] = $html; 
        $data['lateset_orders_count'] = count($latest_orders);
        $html = '';
        if(Auth::user()->can('collection-view')){
          if(count($latest_collection)>0){
            foreach($latest_collection as $ky=>$vl){
              if(is_array($vl['client'])){
                $cname = $vl['client']['company_name'];
                $cid = $vl['client']['id'];
                $collid = $vl['id'];
              }else{
                $cname = '';
                $cid = '';
                $collid = '';
              }
              $paymentdata = '';
              if($vl['payment_method']=='Cheque'){
                $paymentdata = $vl['payment_method'];
                if($vl['payment_status']=='Cleared' || $vl['payment_status']=='Deposited'){
                  $paymentdata .= " <span style='color:green;'>(".$vl['payment_status'].")</span>";
                }else if($vl['payment_status']=='Pending'){
                  $paymentdata .= " <span style='color:blue;'>(".$vl['payment_status'].")</span>";
                }else if($vl['payment_status']=='Overdue'){
                  $paymentdata .= " <span style='color:orange;'>(".$vl['payment_status'].")</span>";
                }else if($vl['payment_status']=='Bounced'){
                  $paymentdata .= " <span style='color:red;'>(".$vl['payment_status'].")</span>";
                }
              }else if($vl['payment_method']!='Cheque' && $vl['payment_method']!='Cash'){
                $paymentdata = $vl['payment_method'];
                if(is_array($vl['bank'])){
                  $bankname = $vl['bank']['name'];
                }else{
                  $bankname = '';
                }
                // $paymentdata .= " <span class='label label-success'>".$bankname."</span>";
              }else if($vl['payment_method']=='Cash'){
                $paymentdata = $vl['payment_method'];
              }
              $html .= "<tr onclick='rowclicked(this)' class='clickable-row' id='".domain_route('company.admin.collection.show',[$collid])."'>";
              // $html .= "<td width='30%'><a onclick='rowclicked(this,\"td\")' id='".domain_route('company.admin.client.show',[$cid])."'>".$cname."</a></td>";
              $html .= "<td width='30%'>".$cname."</td>";
              $html .= "<td width='30%'>".$paymentdata."</td>";
              $html .= "<td width='15%'>".$currency.' '.(number_format($vl['payment_received'], 2))."</td>";
              $html .= "<td width='25%'>".getDeltaDate(Carbon::Parse($vl['created_at'])->format('Y-m-d'))."</td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='3'>No Data Available</td>";
              $html .= "</tr>";
          }
        }
        $data['latest_collection'] = $html;
        $data['latest_collection_count'] = count($latest_collection);

        $html = '';
        if(Auth::user()->can('party-view')){
          if(count($new_addedparties)>0){
            foreach($new_addedparties as $ky=>$vl){
              $d1 = domain_route('company.admin.client.show',[$vl['id']]);
              $html .= "<tr onclick='rowclicked(this)' class='clickable-row' id='".domain_route('company.admin.client.show',[$vl['id']])."'>";
              // $html .= "<td width='35%'><a href='".$d1."'>".$vl['company_name']."</a></td>";
              $html .= "<td width='35%'>".$vl['company_name']."</td>";
              // $html .= "<td width='35%'><a href='".domain_route('company.admin.employee.show',[$vl['eid']])."'>".$vl['ename']."</a></td>";
              $html .= "<td width='35%'>".$vl['ename']."</td>";
              $html .= "<td width='30%'>".getDeltaDate(Carbon::Parse($vl['created_at'])->format('Y-m-d'))."</td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='4'>No Data Available</td>";
              $html .= "</tr>";
          }
        }
        $data['new_addedparties'] = $html;
        $data['new_addedparties_count'] = count($new_addedparties);
        $html = '';
        if(Auth::user()->can('PartyVisit-view')){
          if(count($recent_visits)>0){
            foreach($recent_visits as $ky=>$vl){
              if(is_array($vl['client'])){
                $cname = $vl['client']['company_name'];
                $cid = $vl['client']['id'];
              }else{
                $cname = '';
                $cid = '';
              }
              if(is_array($vl['employee'])){
                $ename = $vl['employee']['name'];
                $eid = $vl['employee']['id'];
              }else{
                $ename = '';
                $eid = '';
              }
              $html .= "<tr onclick='rowclicked(this)' class='clickable-row' id='".domain_route('company.admin.clients.clientVisitDetail',['id' => $cid, 'visit_id' => $vl['id']])."'>";
              // $html .= "<td width='30%'><a href='".domain_route('company.admin.client.show',[$cid])."'>".$cname."</a></td>";
              $html .= "<td width='30%'>".$cname."</td>";
              // $html .= "<td width='30%'><a href='".domain_route('company.admin.employee.show',[$eid])."'>".$ename."</a></td>";
              $html .= "<td width='30%'>".$ename."</td>";
              $html .= "<td width='20%'>".getDeltaDate($vl['date'])."</td>";
              $html .= "<td width='10'>".Carbon::Parse($vl['start_time'])->format('g:i A')."</td>";
              $html .= "</tr>";
            }
          }else{
              $html .= "<tr>";
              $html .= "<td colspan='3'>No Data Available</td>";
              $html .= "</tr>";
          }
        }
        $data['recent_visits'] = $html;
        $data['recent_visits_count'] = count($recent_visits);

        $leaves = Auth::user()->handleQuery('leave')->where('company_id',$company_id)->where('status','Pending')->get()->count();
        $expenses = Auth::user()->handleQuery('expense')->where('company_id',$company_id)->where('status','Pending')->get()->count();
        $tours = Auth::user()->handleQuery('TourPlan')->where('company_id',$company_id)->where('status','Pending')->get()->count();
        $today_date = Carbon::now()->format('Y-m-d');
        $yesterday_date = Carbon::yesterday()->format('Y-m-d');
        $today_visitcount = Auth::user()->handleQuery('clientvisit')->where('company_id',$company_id)->where('date','like',$today_date.'%')->get()->count();
        $yesterday_visitcount = Auth::user()->handleQuery('clientvisit')->where('company_id',$company_id)->where('date','like',$yesterday_date.'%')->get()->count();

        $data['sideticker'] = '';
        $today = date('Y-m-d');
        $attendances = Auth::user()->handleQuery('attendance')->select('employee_id')
            ->where('adate', '=', $today)->where('check_type', 1)->distinct('employee_id')->get()->toArray();
        if(empty($attendances)){
            $present_employees = 0;
        }else{
            $present_employees = sizeof($attendances);
        }

        if(config('settings.monthly_attendance')==1){
            if(Auth::user()->can('monthly-attendance-view')){
              $data['sideticker'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.todayattendancereport').'"><img src="assets/custom_field_icons/present_today.svg" width="20px" >&nbsp; Present Today
                                    <span class="label label-primary pull-right">'.$present_employees.'</span></a></li>';
            }
        }

        if(config('settings.visit_module')==1){
            if(Auth::user()->can('PartyVisit-view')){
              $data['sideticker'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.clientvisit.index').'"><img src="assets/custom_field_icons/noof_visitors_today.svg" width="20px" >&nbsp;No. of Visits Today
                                    <span class="label label-info pull-right">'.$today_visitcount.'</span></a></li>';
              $data['sideticker'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.clientvisit.index').'"><img src="assets/custom_field_icons/noof_visitors_yester.svg" width="20px" >&nbsp;No. of Visits Yesterday
                                    <span class="label label-warning pull-right">'.$yesterday_visitcount.'</span></a></li>';
            }   
        }  

        if(config('settings.leaves')==1){  
            if(Auth::user()->can('leave-view')){             
              if($leaves>0){
                  $data['sideticker'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.leave').'"><img src="assets/custom_field_icons/leaves_tobe_approved.svg" width="20px" >&nbsp;Leaves to be Approved
                                      <span class="label label-danger pull-right">'.$leaves.'</span></a></li>';
              }
            }
        }

        if(config('settings.expenses')==1){
            if(Auth::user()->can('expense-view')){
              if($expenses>0){
                $data['sideticker'] .=  '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.expense').'"><img src="assets/custom_field_icons/expense_tobe_approved.svg" width="20px" >&nbsp;Expenses to be Approved
                                      <span class="label label-danger pull-right">'.$expenses.'</span></a></li>';
              }
            }
        }

        if(config('settings.tour_plans')==1){
            if(Auth::user()->can('tourplan-view')){
              if($tours>0){
                $data['sideticker'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.tours').'"><img src="assets/custom_field_icons/tours_tobe_approved.svg" width="20px" >&nbsp;Tours to be Approved
                                      <span class="label label-danger pull-right">'.$tours.'</span></a></li>';
              }
            }
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $todayDate = Carbon::now()->format('Y-m-d');
        $empid = Auth::user()->id;
        $scheduled_activities = $this->findactivities($empid)->where('activities.start_datetime','>=',$todayDate)->whereNull('activities.completion_datetime')->get()->count();
        $overdue_activities = $this->findactivities($empid)->where('activities.start_datetime','<',$todayDate)->where('activities.completion_datetime','=',NULL)->get()->count();
        $completed_activities = $this->findactivities($empid)->where('activities.completion_datetime','!=',Null)->get()->count();
        $today_activities = $this->findactivities($empid)->where('activities.start_datetime','>=',$todayDate)
        ->where('activities.start_datetime','<=',$todayDate.' 23:59:59')->where('activities.completion_datetime','=',NULL)->get()->count();
        $data['sideticker2'] = '';
        if($scheduled_activities>0){$scclass='warning'; }else{ $scclass='primary'; }
        if(config('settings.activities')==1){
            if(Auth::user()->can('activity-view')){
              $data['sideticker2'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.activities.index').'"><img src="assets/custom_field_icons/scheduled.svg" width="17px" >&nbsp;Scheduled
                                      <span class="label label-'.$scclass.' pull-right">'.$scheduled_activities.'</span></a></li>';
              if($overdue_activities>0){$odclass='danger'; }else{ $odclass='primary'; }
              $data['sideticker2'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.activities.index').'"><img src="assets/custom_field_icons/overdue.svg" width="17px" >&nbsp;Overdue
                                      <span class="label label-'.$odclass.' pull-right">'.$overdue_activities.'</span></a></li>';
              if($completed_activities>0){$caclass='success'; }else{ $caclass='primary'; }                       
              $data['sideticker2'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.activities.index').'"><img src="assets/custom_field_icons/completed.svg" width="17px" >&nbsp;Completed
                                      <span class="label label-'.$caclass.' pull-right"> '.$completed_activities.'</span></a></li>';
              if($today_activities>0){$taclass='danger'; }else{ $taclass='primary'; }
              $data['sideticker2'] .= '<li style="font-weight:bold;"><a target="_blank" href="'.domain_route('company.admin.activities.index').'"><img src="assets/custom_field_icons/due_today.svg" width="17px" >&nbsp;Due Today
                                      <span class="label label-'.$taclass.' pull-right">'.$today_activities.'</span></a></li>';
            }
        }

        return $this->sendResponse($data, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function newPartiesAdded(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;
        for($i=0;$i<count($dates);$i++){
          $collection = Client::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topProductsOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topproducts = Order::select('products.product_name',DB::raw('sum(orderproducts.quantity) as totcount'))
                      ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                      ->Join('products', 'orderproducts.product_id', 'products.id')
                      ->where('orders.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->whereNull('orderproducts.deleted_at')
                      ->groupby('products.id')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();
        $topproducts_data = $topproducts_value = array();
        if(count($topproducts->toArray())>0){
          foreach($topproducts->toArray() as $ky=>$vl){
            $topproducts_data[] = $vl['product_name'];
            $topproducts_value[] = $vl['totcount'];
          }
        }
        $response = array(
          'dates' => $topproducts_data,
          'values' => $topproducts_value,
        );
        if(config('settings.product')==1 && config('settings.orders')==1){
          if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topBrandsOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topbrands = Order::select('brands.name',DB::raw('sum(orderproducts.quantity) as totcount'))
                      ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                      ->Join('products', 'orderproducts.product_id', 'products.id')
                      ->Join('brands', 'products.brand', 'brands.id')
                      ->where('orders.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->whereNull('orderproducts.deleted_at')
                      ->groupby('products.brand')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();

        $topbrands_data = $topbrands_value = array();
        if(count($topbrands->toArray())>0){
          foreach($topbrands->toArray() as $ky=>$vl){
            $topbrands_data[] = $vl['name'];
            $topbrands_value[] = $vl['totcount'];
          }
        }
        $response = array(
          'dates' => $topbrands_data,
          'values' => $topbrands_value,
        );
        if(config('settings.product')==1 && config('settings.orders')==1){
          if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topCategoriesOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topcategories = Order::select('categories.name',DB::raw('sum(orderproducts.quantity) as totcount'))
                      ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                      ->Join('products', 'orderproducts.product_id', 'products.id')
                      ->Join('categories','products.category_id','categories.id')
                      ->where('orders.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->whereNull('orderproducts.deleted_at')
                      ->groupby('products.category_id')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();

        $topcategories_data = $topcategories_value = array();
        if(count($topcategories->toArray())>0){
          foreach($topcategories->toArray() as $ky=>$vl){
            $topcategories_data[] = $vl['name'];
            $topcategories_value[] = $vl['totcount'];
          }
        }
        $response = array(
          'dates' => $topcategories_data,
          'values' => $topcategories_value,
        );
        if(config('settings.product')==1 && config('settings.orders')==1){
          if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function collectionQty(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){
          $collection = Collection::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topPerformBeatsOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topbeats = Beat::select('beats.name',DB::raw('count(orders.id) as totcount'))
                      ->Join('beat_client', 'beats.id', 'beat_client.beat_id')
                      ->Join('orders', 'beat_client.client_id', 'orders.client_id')
                      ->where('beats.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->groupby('beats.name')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();
        $topbeats_data = $topbeats_value = array();
        if(count($topbeats->toArray())>0){
          foreach($topbeats->toArray() as $ky=>$vl){
            $topbeats_data[] = $vl['name'];
            $topbeats_value[] = $vl['totcount'];
          }
        }
        $response = array(
          'dates' => $topbeats_data,
          'values' => $topbeats_value,
        );
        if(config('settings.orders')==1){
          if(Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topProductsAmount(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topproducts = Order::select('products.product_name',DB::raw('sum(orderproducts.amount) as totcount'))
                ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                ->Join('products', 'orderproducts.product_id', 'products.id')
                ->where('orders.company_id', $company_id)
                ->whereBetween('orders.order_date',[$startdate,$enddate])
                ->whereNull('orderproducts.deleted_at')
                ->groupby('products.id')
                ->orderby('totcount','desc')
                ->take(10)
                ->get();
        $topproducts_data = $topproducts_value = array();
        if(count($topproducts->toArray())>0){
          foreach($topproducts->toArray() as $ky=>$vl){
            $topproducts_data[] = $vl['product_name'];
            $topproducts_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topproducts_data,
          'values' => $topproducts_value,
          'currency_symbol' => $currency
        );
        if(Auth::user()->can('product-view')){
          return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topBrandsAmount(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topbrands = Order::select('brands.name',DB::raw('sum(orderproducts.amount) as totcount'))
                      ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                      ->Join('products', 'orderproducts.product_id', 'products.id')
                      ->Join('brands', 'products.brand', 'brands.id')
                      ->where('orders.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->whereNull('orderproducts.deleted_at')
                      ->groupby('products.brand')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();

        $topbrands_data = $topbrands_value = array();
        if(count($topbrands->toArray())>0){
          foreach($topbrands->toArray() as $ky=>$vl){
            $topbrands_data[] = $vl['name'];
            $topbrands_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topbrands_data,
          'values' => $topbrands_value,
          'currency_symbol' => $currency
        );
        if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
          return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }
    
    public function topCategoriesAmount(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topcategories = Order::select('categories.name',DB::raw('sum(orderproducts.amount) as totcount'))
                      ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                      ->Join('products', 'orderproducts.product_id', 'products.id')
                      ->Join('categories','products.category_id','categories.id')
                      ->where('orders.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->whereNull('orderproducts.deleted_at')
                      ->groupby('products.category_id')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();
        $topcategories_data = $topcategories_value = array();
        if(count($topcategories->toArray())>0){
          foreach($topcategories->toArray() as $ky=>$vl){
            $topcategories_data[] = $vl['name'];
            $topcategories_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topcategories_data,
          'values' => $topcategories_value,
          'currency_symbol' => $currency
        );
        if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
          return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function collectionAmount(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){
          $collection = Collection::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->where('payment_status','Cleared')->sum('collections.payment_received');
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topPerformBeatsAmt(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topbeats = Beat::select('beats.name',DB::raw('sum(orders.grand_total) as totcount'))
                      ->Join('beat_client', 'beats.id', 'beat_client.beat_id')
                      ->Join('orders', 'beat_client.client_id', 'orders.client_id')
                      ->where('beats.company_id', $company_id)
                      ->whereBetween('orders.order_date',[$startdate,$enddate])
                      ->groupby('beats.name')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();
        $topbeats_data = $topbeats_value = array();
        if(count($topbeats->toArray())>0){
          foreach($topbeats->toArray() as $ky=>$vl){
            $topbeats_data[] = $vl['name'];
            $topbeats_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topbeats_data,
          'values' => $topbeats_value,
          'currency_symbol' => $currency
        );
        if(config('settings.orders')==1 ){
          if(Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topPerformBeatsCollAmt(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topbeats = Beat::select('beats.name',DB::raw('sum(collections.payment_received) as totcount'))
                      ->Join('beat_client', 'beats.id', 'beat_client.beat_id')
                      ->Join('collections', 'beat_client.client_id', 'collections.client_id')
                      ->where('beats.company_id', $company_id)
                      ->whereBetween('collections.payment_date',[$startdate,$enddate])
                      ->where('collections.payment_status','Cleared')
                      ->groupby('beats.name')
                      ->orderby('totcount','desc')
                      ->take(10)
                      ->get();
        $topbeats_data = $topbeats_value = array();
        if(count($topbeats->toArray())>0){
          foreach($topbeats->toArray() as $ky=>$vl){
            $topbeats_data[] = $vl['name'];
            $topbeats_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topbeats_data,
          'values' => $topbeats_value,
          'currency_symbol' => $currency
        );
        if(config('settings.collections')==1){
          if(Auth::user()->can('collection-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['dates'] = [];
            $response['values'] = [];
            return response()->json([
                  'success' => true,
                  'data' => $response,
                  'message' => 'Failed Authorization'
              ],200);
          }
        }else{
          $response['dates'] = [];
          $response['values'] = [];
          return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Failed Authorization'
            ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function timeSpentVisit(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;
        $new_collection_data = $new_collection_data_yearly = $new_collection_data_monthly = [];
        for($i=0;$i<count($dates);$i++){
          $clientvisittime = ClientVisit::where('date',$dates[$i])->where('company_id',$company_id)->get()->toArray();
          // if(count($clientvisittime)>0){
            $visittime = 0;
            foreach($clientvisittime as $kk=>$lv){
              $start_time = Carbon::parse($clientvisittime[$kk]['start_time']);
              $end_time = Carbon::parse($clientvisittime[$kk]['end_time']);
              $visittime += $end_time->diffInSeconds($start_time);
            }
            $collection = $visittime;           
            $month = Carbon::parse($dates[$i])->format('M');
            $year = Carbon::parse($dates[$i])->format('Y');
            array_push($month_array, $month);
            if($request->type=="nepali"){
              $expDate = explode('-',$dates[$i]);
              $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
              $nepDate = explode('-',$nepDate);
              $month = $this->neDate->getNepaliMonthName($nepDate[1]);
              $year = $nepDate[0];
            }
            array_push($custom_year_array, $year);
            $new_collection_data_yearly[][$year] = (int) $collection;
            $new_collection_data_monthly[][$month] = (int) $collection;
            $new_collection_data[][$dates[$i]] = (int) $collection;
          // }
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('PartyVisit-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topPartiesByCollAmt(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        // $topparties = PartyType::select('partytypes.name',DB::raw('sum(collections.payment_received) as totcount'))
        //         ->Join('clients', 'partytypes.id', 'clients.client_type')
        //         ->Join('collections', 'clients.client_code', 'collections.client_id')
        //         ->where('partytypes.company_id', $company_id)
        //         ->whereBetween('collections.payment_date',[$startdate,$enddate])
        //         ->groupby('partytypes.parent_id')
        //         ->orderby('totcount','desc')
        //         ->take(10)
        //         ->get();
        $topparties = Client::select('clients.company_name',DB::raw('sum(collections.payment_received) as totcount'))
                ->Join('collections', 'clients.id', 'collections.client_id')
                ->where('clients.company_id', $company_id)
                ->whereBetween('collections.payment_date',[$startdate,$enddate])
                ->whereNull('collections.deleted_at')
                ->groupby('clients.id')
                ->orderby('totcount','desc')
                ->take(10)
                ->get();
        $topparties_data = $topparties_value = array();
        if(count($topparties->toArray())>0){
          foreach($topparties->toArray() as $ky=>$vl){
            $topparties_data[] = $vl['company_name'];
            $topparties_value[] = $vl['totcount'];
          }
        }

        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topparties_data,
          'values' => $topparties_value,
          'currency_symbol' => $currency
        );
        if(config('settings.collections')==1){
          if(Auth::user()->can('collection-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
          return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topPartiesByOrderValue(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        // $topparties = PartyType::select('partytypes.name',DB::raw('sum(orders.grand_total) as totcount'))
        //         ->Join('clients', 'partytypes.id', 'clients.client_type')
        //         ->Join('orders', 'clients.id', 'orders.client_id')
        //         ->where('partytypes.company_id', $company_id)
        //         ->whereBetween('orders.order_date',[$startdate,$enddate])
        //         ->groupby('partytypes.parent_id')
        //         ->orderby('totcount','desc')
        //         ->take(10)
        //         ->get();
        $topparties = Client::select('clients.company_name',DB::raw('sum(orders.grand_total) as totcount'))
                ->Join('orders', 'clients.id', 'orders.client_id')
                ->where('clients.company_id', $company_id)
                ->whereBetween('orders.order_date',[$startdate,$enddate])
                ->whereNull('orders.deleted_at')
                ->groupby('clients.id')
                ->orderby('totcount','desc')
                ->take(10)
                ->get(); 
        $topparties_data = $topparties_value = array();
        if(count($topparties->toArray())>0){
          foreach($topparties->toArray() as $ky=>$vl){
            $topparties_data[] = $vl['company_name'];
            $topparties_value[] = $vl['totcount'];
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topparties_data,
          'values' => $topparties_value,
          'currency_symbol' => $currency
        );
        if(config('settings.orders')==1){
          if(Auth::user()->can('order-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topExpenses(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $topexpenses = ExpenseType::select('expense_types.expensetype_name',DB::raw('sum(expenses.amount) as totexpense'))
                                  ->Join('expenses','expense_types.id','expenses.expense_type_id')
                                  ->whereBetween('expenses.expense_date',[$startdate,$enddate])
                                  ->where('expenses.company_id',$company_id)
                                  ->where('expenses.status','Approved')
                                  ->groupby('expense_types.id')
                                  ->get()->toArray();
        $keys = array_column($topexpenses, 'totexpense');
        array_multisort($keys, SORT_DESC, $topexpenses); 
        $topexpenses_data = $topexpenses_value = array();
        if(count($topexpenses)>0){
          foreach($topexpenses as $ky=>$vl){
            if($ky==10){
              break;
            }else{
              $topexpenses_data[] = $vl['expensetype_name'];
              $topexpenses_value[] = round($vl['totexpense'],2);
            }
          }
        }
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        $response = array(
          'dates' => $topexpenses_data,
          'values' => $topexpenses_value,
          'currency_symbol' => $currency
        );
        if(config('settings.expenses')==1){
          if(Auth::user()->can('expense-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Order::where('order_date',$dates[$i])->where('company_id',$company_id)->get()->count();

          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('order-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                    'success' => true,
                    'data' => 'nok',
                    'message' => 'Failed Authorization'
                ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function zeroOrder(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){
          $collection = NoOrder::where('date',$dates[$i])->where('company_id',$company_id)->get()->count();
                    
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('zeroorder-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                  'success' => true,
                  'data' => 'nok',
                  'message' => 'Failed Authorization'
              ],200);
        }
      }catch (\Exception $error) {
        dd($error);
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function prodCalls(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  Carbon::now()->subDays(30)->format('Y-m-d').' 00:00:00';
          $endDate = Carbon::now()->format('Y-m-d').' 00:00:00';
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Order::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->get()->count();

          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalCalls(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){
          $collection = NoOrder::where('date',$dates[$i])->where('company_id',$company_id)->get()->count();
          $collection2 = Order::where('order_date',$dates[$i])->where('company_id',$company_id)->get()->count();

          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = ((int)$collection+(int)$collection2);
          $new_collection_data_monthly[][$month] = ((int)$collection+(int)$collection2);
          $new_collection_data[][$dates[$i]] = ((int)$collection+(int)$collection2);
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                    'success' => true,
                    'data' => 'nok',
                    'message' => 'Failed Authorization'
                ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalVisits(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = ClientVisit::where('date',$dates[$i])->where('company_id',$company_id)->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('PartyVisit-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                  'success' => true,
                  'data' => 'nok',
                  'message' => 'Failed Authorization'
              ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totProdSold(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Order::select('products.product_name',DB::raw('count(orderproducts.id) as totcount'))
                            ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                            ->Join('products', 'orderproducts.product_id', 'products.id')
                            ->where('orders.company_id', $company_id)
                            ->where('orders.order_date',$dates[$i])
                            ->groupby('orderproducts.product_id')
                            ->orderby('totcount','desc')
                            ->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('product-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                  'success' => true,
                  'data' => 'nok',
                  'message' => 'Failed Authorization'
              ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalPartiesAdded(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Client::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('party-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          return response()->json([
                        'success' => true,
                        'data' => 'nok',
                        'message' => 'Failed Authorization'
                    ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalCollections(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Collection::where('created_at','like',$dates[$i].'%')->where('company_id',$company_id)->get()->count();
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalOrderAmount(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;

        for($i=0;$i<count($dates);$i++){        
          $collection = Order::where('order_date',$dates[$i])->where('company_id',$company_id)->sum('grand_total');
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('order-view')){
          if(config('settings.order_with_amt')==0){
            return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalCollAmt(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startDate =  strtotime(Carbon::now()->subDays(30)->format('Y-m-d'));
          $endDate = strtotime(Carbon::now()->format('Y-m-d'));
        }else{
          $startDate = strtotime($st_date);
          $endDate = strtotime($ed_date);
        }
        $dateArray = [];
        for($i=$startDate; $i <= $endDate; $i+=86400) { 
            array_push($dateArray, date("Y-m-d", $i));
        }
        $dates = $dateArray;
        for($i=0;$i<count($dates);$i++){        
          $collection = Collection::where('payment_date',$dates[$i])->where('company_id',$company_id)->where('payment_status','Cleared')->sum('payment_received');
          $month = Carbon::parse($dates[$i])->format('M');
          $year = Carbon::parse($dates[$i])->format('Y');
          array_push($month_array, $month);
          if($request->type=="nepali"){
            $expDate = explode('-',$dates[$i]);
            $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
            $nepDate = explode('-',$nepDate);
            $month = $this->neDate->getNepaliMonthName($nepDate[1]);
            $year = $nepDate[0];
          }
          array_push($custom_year_array, $year);
          $new_collection_data_yearly[][$year] = (int) $collection;
          $new_collection_data_monthly[][$month] = (int) $collection;
          $new_collection_data[][$dates[$i]] = (int) $collection;
        }
        $diffStartDate = Carbon::parse($dates[0]);
        $diffEndDate = Carbon::parse($dates[count($dates)-1]);
        $diff = $diffStartDate->diffInMonths($diffEndDate);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        if ($diffDays > 45) {
          $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
        } else {
          $response = $this->logicLayer($new_collection_data);
        }
        if(Auth::user()->can('collection-view')){
          return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function totalZeroOrderPie(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        $total_orders = Order::whereBetween('order_date',[$startdate,$enddate])->where('company_id',$company_id)->orderby('created_at','desc')->get()->count();
        $no_zero_orders = NoOrder::whereBetween('date',[$startdate,$enddate])->where('company_id',$company_id)->get()->count();
        $response = array(
          'dates' => ['Zero Orders','Total Orders'],
          'values' => [$no_zero_orders,$total_orders],
        );
        if(config('settings.orders')==1 && config('settings.zero_orders')==1){
          if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function topOutlets(Request $request){
      $month_array = [];
      $custom_year_array = [];
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d');
          $enddate = Carbon::now()->format('Y-m-d');
        }else{
          $startdate = $st_date;
          $enddate = $ed_date;
        }
        // echo $startdate.' '.$enddate;die();
        // $total_outlets = Client::where('company_id',$company_id)->where('status','Active')->orderby('created_at','desc')->count();
        $parties = DB::select("select p.id,p.name,count(c.id) as clients FROM `partytypes` as p inner join clients as c on p.id=c.client_type where c.status='Active' and p.company_id=? and c.deleted_at IS NULL group by p.name order by clients desc", array($company_id));
        $parties_unspecified = Client::where('company_id',$company_id)->where('status','Active')->where('client_type',0)->count();
        $totparties = 0;
        if(count($parties)>0){
            foreach($parties as $ky=>$vl){
              $totparties += (int)$vl->clients;
          }
        }
        $total_outlets = ($totparties+$parties_unspecified);
        $order_outlets = Order::whereBetween('order_date',[$startdate,$enddate])->where('company_id',$company_id)->groupby('client_id')->get()->toArray();
        $outlets = 0;
        if(count($order_outlets)>0){
            foreach($order_outlets as $km=>$vm){
                $outlets += 1;
            }
        }
        $response = array(
          'dates' => ['Parties without Order','Parties with Order'],
          'values' => [($total_outlets-$outlets),$outlets],
        );
        if(config('settings.retailer_app')==1){
          if(Auth::user()->can('outlet-view')){
            return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
          }else{
            $response['values'] = [];
            return response()->json([
                        'success' => true,
                        'data' => $response,
                        'message' => 'Failed Authorization'
                    ],200);
          }
        }else{
          $response['values'] = [];
          return response()->json([
                      'success' => true,
                      'data' => $response,
                      'message' => 'Failed Authorization'
                  ],200);
        }
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function convert_targetinterval($intervalid,$intervalvalue){
      $totaldays_inmonth = 30;//taking average total working days to be 30
      switch($intervalid){
        case 1:
          $targetfor_forday= $intervalvalue;
          return $targetfor_forday;
          break;
        case 2:
          $targetfor_forday = ($intervalvalue/7);
          return $targetfor_forday;
          break;
        case 3:
          $targetfor_forday = ($intervalvalue/$totaldays_inmonth);
          return $targetfor_forday;
          break;
      }
    }

    public function targetsGaugeData(Request $request){
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        $company_id = config('settings.company_id');;
        if(empty($st_date)){
          $startdate =  Carbon::now()->subDays(30)->format('Y-m-d').' 00:00:00';
          $enddate = Carbon::now()->format('Y-m-d').' 00:00:00';
        }else{
          $startdate = $st_date.' 00:00:00';
          $enddate = $ed_date.' 00:00:00';
        }
        $targets_data = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',1)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetnoorder_toachieve = $daily_targets = 0;
        foreach($targets_data->toArray() as $tg=>$gt){
          $target_interval = $gt['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $holiday = Holiday::where('company_id',$company_id)->whereBetween('start_date',[$st_date,$ed_date])->get()->count();
        $diffStartDate = Carbon::parse($st_date);
        $diffEndDate = Carbon::parse($ed_date);
        $diffDays = $diffStartDate->diffInDays($diffEndDate);
        $total_working_days = $diffDays-$holiday;
        $targetnoorder_toachieve = round(($total_working_days*$daily_targets),2);
        $nooforder_data = Order::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetsnoorder_achieved = $nooforder_data->count();

        $targets_data2 = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',3)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetnoofcoll_toachieve = $daily_targets = 0;
        foreach($targets_data2->toArray() as $tgt=>$gtt){
          $target_interval = $gtt['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $targetnoofcoll_toachieve = round(($total_working_days*$daily_targets),2);
        $noofcoll_data = Collection::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetsnocoll_achieved = $noofcoll_data->count();
        
        $targets_data3 = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',5)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetnoofvisit_toachieve = $daily_targets = 0;
        foreach($targets_data3->toArray() as $tbt=>$gbt){
          $target_interval = $gbt['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $targetnoofvisit_toachieve = round(($total_working_days*$daily_targets),2);
        $noofvisit_data = ClientVisit::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetsnovisit_achieved = $noofvisit_data->count();

        $targets_data4 = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',2)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetvalueorder_toachieve = $daily_targets = 0;
        foreach($targets_data4->toArray() as $tgp=>$gtp){
          $target_interval = $gtp['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $targetvalueorder_toachieve = round(($total_working_days*$daily_targets),2);
        $valurorder_data = Order::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->sum('payment_received');
        $targetsvalueorder_achieved = $valurorder_data;

        $targets_data5 = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',4)->whereBetween('created_at',[$startdate,$enddate])->get();
        $targetvaluecoll_toachieve = $daily_targets = 0;
        foreach($targets_data5->toArray() as $tgp=>$gtp){
          $target_interval = $gtp['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $targetvaluecoll_toachieve = round(($total_working_days*$daily_targets),2);
        $valuecoll_data = Collection::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->sum('payment_received');
        $targetsvaluecoll_achieved = $valuecoll_data;

        $targets_data7 = TargetSalesmanassign::where('company_id',$company_id)->where('target_rule',7)->whereBetween('created_at',[$startdate,$enddate])->get();
        $totalcalls_toachieve = $daily_targets = 0;
        foreach($targets_data7->toArray() as $tgp=>$gtp){
          $target_interval = $gtp['target_interval'];
          $daily_targets += $this->convert_targetinterval($company_id,$target_interval,$startdate,$enddate);
        }
        $totalcalls_toachieve = round(($total_working_days*$daily_targets),2);
        $zeroorder_data = NoOrder::where('company_id',$company_id)->whereBetween('created_at',[$startdate,$enddate])->get();
        $zeroordr_count = $zeroorder_data->count();
        $totalcalls_achieved = $zeroordr_count+$targetsnoorder_achieved;

        $response = array(
          'targetsnoorder_achieved' => $targetsnoorder_achieved,
          'targetsnoorder_toachieve' => $targetnoorder_toachieve,
          'targetsnocoll_achieved' => $targetsnocoll_achieved,
          'targetsnocoll_toachieve' => $targetnoofcoll_toachieve,
          'targetsnovisit_achieved' => $targetsnovisit_achieved,
          'targetsnovisit_toachieve' => $targetnoofvisit_toachieve,
          'targetsvalueorder_achieved' => $targetsvalueorder_achieved,
          'targetvalueorder_toachieve' => $targetvalueorder_toachieve,
          'targetvaluecoll_toachieve' => $targetvaluecoll_toachieve,
          'targetsvaluecoll_achieved' => $targetsvaluecoll_achieved,
          'totalcalls_toachieve' => $totalcalls_toachieve,
          'totalcalls_achieved' => $totalcalls_achieved,
          'targetgoldencalls_toachieve' => $targetnoorder_toachieve,
          'targetgoldencalls_achieved' => $targetsnoorder_achieved,
        );
        return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }

    public function clientVisitLocations(Request $request){
      try{
        $st_date = $request->start_date;
        $ed_date = $request->end_date;
        if(empty($st_date)){
          $st_date = Carbon::now()->startOfMonth()->subMonth()->toDateString();;
          $ed_date = Carbon::now()->format('Y-m-d');
        }else{
          $st_date = $st_date;
          $ed_date = $ed_date;
        }
        $company_id = config('settings.company_id');
        $clientdetails = array();
        $client_visit = Client::where('company_id',$company_id)->whereNOTNULL('latitude')->whereNOTNULL('longitude')->groupby('id')->get(['id as client_id','latitude','longitude','company_name'])->toArray();
        $currency = (config('settings.currency_symbol')!='')?(config('settings.currency_symbol')):config('settings.default_currency');
        if(count($client_visit)>0){
          foreach($client_visit as $yh=>$hy){
            $clientid = $hy['client_id'];
            $clientdetails[$yh]['latitude'] = $client_visit[$yh]['latitude'];
            $clientdetails[$yh]['longitude'] = $client_visit[$yh]['longitude'];
            $clientdetails[$yh]['clientname'] = $client_visit[$yh]['company_name'];
            $clientdetails[$yh]['total_orders'] = Order::where('company_id',$company_id)->whereBetween('order_date',[$st_date,$ed_date])->where('client_id',$clientid)->get()->count();
            $clientdetails[$yh]['noorder'] = NoOrder::where('company_id',$company_id)->whereBetween('date',[$st_date,$ed_date])->where('client_id',$clientid)->get()->count();
            $clientdetails[$yh]['visits'] = ClientVisit::where('company_id',$company_id)->whereBetween('date',[$st_date,$ed_date])->where('client_id',$clientid)->get()->count();
            $clientdetails[$yh]['ordervalue'] = Order::where('company_id',$company_id)->whereBetween('order_date',[$st_date,$ed_date])->where('client_id',$clientid)->sum('grand_total');
            $clientdetails[$yh]['collvalue'] = Collection::where('company_id',$company_id)->whereBetween('payment_date',[$st_date,$ed_date])->where('client_id',$clientid)->where('payment_status','Cleared')->sum('payment_received');
            $totalvisits = ClientVisit::whereBetween('date',[$st_date,$ed_date])->where('client_id',$clientid)->where('company_id',$company_id)->get();
            $tot_visittime = '';$tot_time = 0;
            foreach($totalvisits->toArray() as $ret=>$ter){
                $stime = strtotime($ter['start_time']);
                $etime = strtotime($ter['end_time']);
                $tot_time += $etime-$stime;
            }
            if($tot_time>=0 && $tot_time<=60){
                $tot_visittime = $tot_time.' seconds ';
            }else if($tot_time>60 && $tot_time<=3600){
                $minutes = round((int)($tot_time/60),2).' minutes ';
                $second = round((int)($tot_time%60),2).' seconds';
                if($second==0){
                $tot_visittime = $minutes;
                }else{
                $tot_visittime = $minutes.$second;
                }
            }else{
                $hours = round((int)($tot_time/3600),2).' Hrs ';
                $minutes = round((int)($tot_time%60),2).' minutes';
                if($minutes==0){
                    $tot_visittime = $hours;
                }else{
                    $tot_visittime = $hours.$minutes;
                }
            }
            $clientdetails[$yh]['visittimespent'] = $tot_visittime;
          }
        }
        $response = array(
          'client_visit' => $clientdetails,
          'currency_symbol' => $currency
        );
        return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
      }catch (\Exception $error) {
        return $this->sendError($error, $error->getMessage());
      }
    }
 
    public function dashboardAnalytics(Request $request){
      if((config('settings.analytics_new')==1) && (Auth::user()->employee->is_admin==1)){
        $company_id = config('settings.company_id');
        $empid = Auth::user()->id;
        $analyticssave = AnalyticsSave::where('company_id',$company_id)->where('employee_id',$empid)->where('mapkey','like','storeddate%')->get()->toArray();
        $storeddate = array();
        if(count($analyticssave)>0){
            $storeddate['chktype'] = 'available';
            $storeddate['mapvalue'] = $analyticssave[0]['mapvalue'];
        }else{
            $storeddate['chktype'] = 'notavailable';
            $storeddate['mapvalue'] = '';
        }
        return view('company.admin.analytics2',compact('storeddate'));
      }else{
        return redirect()->route('company.admin.home',['domain' => domain()])->with(['error'=>'You don\'t have sufficient permission to view this content. ']);
      }
    }

    public function fetchAnalyticsSave(Request $request){
        $company_id = config('settings.company_id');
        $empid = Auth::user()->id;
        $searchstring = $request->searchstr;
        $analyticssave = AnalyticsSave::where('company_id',$company_id)->where('employee_id',$empid)->where('mapkey','like',$searchstring.'%')->get()->toArray();
        if(count($analyticssave)==0){
            if($searchstring=="storeddate"){
                $response = array(
                  'chktype' => 'notavailable',
                  'mapvalue' => '',
                  ); 
            }else{
                $response = array(
                  'chktype' => 'notavailable',
                  'ticker_1' => '',
                  'ticker_2' => 'aa_2',
                  'ticker_3' => 'aa_3',
                  'ticker_4' => 'aa_4',
                  ); 
            }
        }else{
            if($searchstring=="storeddate"){
                $mapvalue = $analyticssave[0]['mapvalue'];
                $response = array(
                  'chktype' => 'available',
                  'mapvalue' => $mapvalue,
                  );
            }else{
                $response = array();
                foreach($analyticssave as $ty=>$yt){
                    foreach($yt as $re=>$er){
                        if($re=='mapkey'){
                            $response[$yt['mapkey']] = $yt['mapvalue'];
                        }
                    }
                }
                $response['chktype'] = 'available';
            }
        }
        return $this->sendResponse($response, "Sent Successful!!!. MANJIT");
    }

    public function saveDateFilter(Request $request){
        $company_id = config('settings.company_id');
        $empid = Auth::user()->id;
        $searchstring = $request->searchstr;
        $insvalue = $request->datefiltertype;
        $analyticssave = AnalyticsSave::where('company_id',$company_id)->where('employee_id',$empid)->where('mapkey','like',$searchstring.'%');
        $analyticssave->delete();
        $analytics = new AnalyticsSave;
        $analytics->company_id = $company_id;
        $analytics->employee_id = $empid;
        $analytics->mapkey = $searchstring;
        $analytics->mapvalue = $insvalue;
        $analytics->save();
    }

}
