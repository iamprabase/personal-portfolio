<?php

namespace App\Http\Controllers\API;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Company\Admin\TargetSalesmanController;
use Carbon\Carbon;
use App\Employee;
use App\Holiday;
use App\Order;
use App\Client;
use App\NoOrder;
use App\Collection;
use App\ClientVisit;
use App\TargetSalesman;
use App\TargetSalesmanassign;
use Auth;
use DB;
use StdClass;
use DateTime;
 
class TargetAchieveController extends Controller
{
	public function __construct(){
		$this->middleware('auth:api');
  }
 

  public function APItest(){
    $response = array("status" => true, "message" => "Success API test method, MANJIT", "data" => '[MANJIT]');
    return json_encode($response);
  }

  public function index2(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
    $employee_withtargets = array();
    $salesmanid = ($request->input('id'))?($request->input('id')):'a';
    if($salesmanid!='a'){
      $saleman_exist = Employee::where('company_id',$companyID)->where('id',$salesmanid)->get();
      if(count($saleman_exist)==0){
        $response = array("status" => true, "message" => "Error, User not Found.", "data" => null);
        return json_encode($response);
      }else{
        $employee_withtargets = Employee::has('targetsalesmanassign')->where('company_id', $companyID)->where('status', '=', 'Active')->where('id',$salesmanid)->get();  
      }
    }else{
      $response = array("status" => true, "message" => "Error, Request without user.", "data" => null);
      return json_encode($response);
    }
    $arrange_targets = $salesmantgt_dates = array();
    $datestart_thismonth = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
    $dateend_thismonth = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';

    foreach($employee_withtargets as $indx=>$emptgt){
      $salesmantgt = $emptgt->targetsalesmanassign->toArray();
      $salesmantgt_dates = array();
      foreach($salesmantgt as $aa=>$bb){
        $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
        $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
        $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
        
        $order_rule = $salesmantgt[$aa]['target_rule'];
        switch($order_rule){
          case 1:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart)->get()->count();
              $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->count();
              $all_orders_prev = $tot_order_prev;
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
              $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
              $all_orders_prev = $tot_order_prev;
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
              $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);

            }
            $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $tot_noorder_thismonth = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_order_prev);
            break;
          case 2:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->sum('grand_total');
              $amt_noorder = 0;
              $allamt_orders = $amt_order+$amt_noorder;
              $salesmantgt_dates['results_today'][$order_rule] = (int)($allamt_orders);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->sum('grand_total');
              $allamt_orders_prev = $amt_order_prev;
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($allamt_orders_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
              $amt_noorder = 0;
              $allamt_orders = $amt_order+$amt_noorder;
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($allamt_orders);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->sum('grand_total');
              $allamt_orders_prev = $amt_order_prev;
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($allamt_orders_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
              $amt_noorder = 0;
              $allamt_orders = $amt_order+$amt_noorder;
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($allamt_orders);

              // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
            }
            $amt_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('grand_total');
            $amt_noorder_thismonth = 0;
            $allamt_orders_thismonth = $amt_order_thismonth+$amt_noorder_thismonth;
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($allamt_orders_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->sum('grand_total');
            $allamt_orders_prev = $amt_order_prev;
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($allamt_orders_prev);
            break;
          case 3:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart)->get()->count();
              $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_collection);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->count();
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_collection_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_collection);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->count();
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_collection_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_collection);

              // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
            }
            $tot_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_collection_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->count();
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_collection_prev);
            break;
          case 4:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart.'%')->get()->sum('payment_received');
              $salesmantgt_dates['results_today'][$order_rule] = (int)($amt_collection);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->sum('payment_received');
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($amt_collection_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($amt_collection);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->sum('payment_received');
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($amt_collection_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($amt_collection);
              // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
            }
            $amt_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('payment_received');
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($amt_collection_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->sum('payment_received');
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($amt_collection_prev);
            break;
          case 5:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
              $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_visits);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev)->get()->count();
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_visits_prev);            
              // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();      
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_visits);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_visits_prev);        
              // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();  
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_visits);        
              // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
            }
            $tot_visits_thismonth = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->where('employee_id',$salesmanid)->get()->count();              
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_visits_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();  
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_visits_prev);   
            break; 
          case 6:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
              $salesmantgt_dates['results_today'][$order_rule] = (int)($golder_calls);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart_prev.'%')->get()->count();
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($golder_calls_prev);   
              // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d').' 00:00:00';
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d').' 23:59:59';
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($golder_calls);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d').' 00:00:00';
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d').' 23:59:59';
              $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_prev,$dateend_prev])->get()->count();
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($golder_calls_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d').' 00:00:00';
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($golder_calls);
              // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
            }
            $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($golder_calls);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d').' 00:00:00';
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d").' 23:59:59';
            $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_prev,$dateend_prev])->get()->count();
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($golder_calls_prev);
            break;
          case 7:
            if($salesmantgt[$aa]['target_interval']==1){
              $datestart = Carbon::now()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->count();
              $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

              $datestart_prev = Carbon::yesterday()->format('Y-m-d');
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev.'%')->get()->count();
              $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev.'%')->get()->count();
              $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
              $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);  
              // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==2){
              $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
              $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
              $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

              $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
              $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
              $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
              $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
              $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
              // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
            }elseif($salesmantgt[$aa]['target_interval']==3){
              $datestart = Carbon::now()->firstOfMonth()->format('Y-m-d');
              $dateend = Carbon::now()->endOfMonth()->format('Y-m-d');
              $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
              $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
              $all_orders = $tot_order+$tot_noorder;
              $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);
              // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
            }
            $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $tot_noorder_thismonth = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
            $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
            $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);

            $datestart_prev = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
            $date = new DateTime();
            $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
            $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
            $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
            $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
            $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($all_orders_prev);
            break;
        }
      } 
      //converting interval to current month equivalent
      $tp = 1;
      $aa = Carbon::Parse($datestart_thismonth);
      $bb = Carbon::Parse($dateend_thismonth);
      $totaldays_sofarthismonth = $aa->diffInDays($bb)+1;
      $totalweeks_sofarthismonth = (int)($totaldays_sofarthismonth/7);
      $total_monthdays = Carbon::now()->daysInMonth;
      foreach($salesmantgt_dates['tg_interval'] as $bv=>$vb){
        if($vb==1){
          $ab = Carbon::parse($datestart)->format('Y-m');
          $holidays = Holiday::where('company_id',$companyID)->where('start_date','like',$ab.'%')->get()->toArray();
          $holiday_count = 0;
          foreach($holidays as $h=>$days){
            $std = Carbon::parse($days['start_date']);
            $edd = Carbon::parse($days['end_date']);
            // $holiday_count += $std->diffInDays($edd)+1;
            if($std==$edd){
              $holiday_count += 1;
            }else{
              $holiday_count += ($std->diffInDays($edd))+1;
            }
          } 
          $totaldays_excholidays = $totaldays_sofarthismonth-$holiday_count;
          $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)($salesmantgt_dates['tg_values'][$bv]*$totaldays_excholidays);
        }elseif($vb==2){
          $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/7)*$totaldays_sofarthismonth);
        }elseif($vb==3){
          $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/$total_monthdays)*$totaldays_sofarthismonth);
        }
        $tp++;
      }
    }
    $response = array("status" => true, "message" => "Success", "data" => $salesmantgt_dates);
    return json_encode($response);
  }


  public function index(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
    $employee_withtargets = array();
    $salesmanid = ($request->input('id'))?($request->input('id')):'a';
    if($salesmanid!='a'){
      $saleman_exist = Employee::where('company_id',$companyID)->where('id',$salesmanid)->get();
      if(count($saleman_exist)==0){
        $response = array("status" => true, "message" => "Error, User not Found.", "data" => null);
        return json_encode($response);
      }
    }else{
      $response = array("status" => true, "message" => "Error, Request without user.", "data" => null);
      return json_encode($response);
    }
    $arrange_targets = $salesmantgt_dates = array();

    $date = new DateTime();
    $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
    $tg_startdate = ($request->startdate)?($request->startdate):(Carbon::now()->startOfMonth()->format('Y-m-d'));
    $tg_enddate = ($request->enddate)?($request->enddate):(Carbon::now()->endOfMonth()->format('Y-m-d'));
    $tg_prev_startdate = ($request->startdate_prevmonth)?($request->startdate_prevmonth):(Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d'));
    $tg_prev_enddate = ($request->endate_prevmonth)?($request->endate_prevmonth):($dateend_prev);
    $tg_todaydate = Carbon::now()->format('Y-m-d');

    
    $tgcurmonth_chk = Carbon::Parse($tg_startdate)->format('Y-m');
    $tgprevmonth_chk = Carbon::Parse($tg_prev_startdate)->format('Y-m');
    $employee_withtargets = Employee::where('company_id', $companyID)->where('status', '=', 'Active')->where('id',$salesmanid); 
    $employee_withtargets = $employee_withtargets->with(['targetsalesmanassign' => function($q) use($tgcurmonth_chk){
      $q->where('target_startmonth','like',$tgcurmonth_chk.'%');
    }]);
    $employee_withtargets = $employee_withtargets->with(['targetsalesmanassignhistory' => function($q) use($tgprevmonth_chk){
      $q->where('target_startmonth','like',$tgprevmonth_chk.'%')->orderby('target_assigneddate','desc');
    }]);
    $employee_withtargets = $employee_withtargets->get();

    $salesmantgt = $salesmantgt_hist = array(); 
    foreach($employee_withtargets as $indx=>$emptgt){
      $salesmantgt = $emptgt['targetsalesmanassign']->toArray();

      $salesmantgt_dates = array();
      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart)->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->count();
                $all_orders_prev = $tot_order_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
                $all_orders_prev = $tot_order_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);

              }
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $tot_noorder_thismonth = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
            case 2:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($allamt_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->sum('grand_total');
                $allamt_orders_prev = $amt_order_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($allamt_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($allamt_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->sum('grand_total');
                $allamt_orders_prev = $amt_order_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($allamt_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($allamt_orders);

                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }
              $amt_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->sum('grand_total');
              $amt_noorder_thismonth = 0;
              $allamt_orders_thismonth = $amt_order_thismonth+$amt_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($allamt_orders_thismonth);
              break;
            case 3:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart)->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_collection);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_collection);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_collection);

                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }
              $tot_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_collection_thismonth);
              break;
            case 4:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart.'%')->get()->sum('payment_received');
                $salesmantgt_dates['results_today'][$order_rule] = (int)($amt_collection);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->sum('payment_received');
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($amt_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($amt_collection);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->sum('payment_received');
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($amt_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($amt_collection);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }
              $amt_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_startdate,$tg_todaydate])->get()->sum('payment_received');
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($amt_collection_thismonth);
              break;
            case 5:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_visits);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev)->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_visits_prev);            
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();      
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_visits);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_visits_prev);        
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();  
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_visits);        
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }
              $tot_visits_thismonth = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_startdate,$tg_todaydate])->where('employee_id',$salesmanid)->get()->count();              
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_visits_thismonth);
              break; 
            case 6:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($golder_calls);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart_prev.'%')->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($golder_calls_prev);   
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d').' 00:00:00';
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d').' 23:59:59';
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($golder_calls);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d').' 00:00:00';
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d').' 23:59:59';
                $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($golder_calls_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($golder_calls);
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$tg_startdate,$tg_todaydate])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($golder_calls);
              break;
            case 7:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev.'%')->get()->count();
                $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev.'%')->get()->count();
                $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);  
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
                $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
                $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $tot_noorder_thismonth = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_startdate,$tg_todaydate])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
          }
        } 
        //converting interval to current month equivalent
        $tp = 1;
        $aa = Carbon::Parse($tg_startdate);
        $bb = Carbon::Parse($tg_enddate);
        $totaldays_sofarthismonth = $aa->diffInDays($bb)+1;
        foreach($salesmantgt_dates['tg_interval'] as $bv=>$vb){
          if($vb==1){
            // $ab = Carbon::parse($datestart)->format('Y-m');
            $holidays = Holiday::where('company_id',$companyID)->whereBetween('start_date',[$tg_startdate,$tg_enddate])->get()->toArray();
            $holiday_count = 0;
            foreach($holidays as $h=>$days){
              $std = Carbon::parse($days['start_date']);
              $edd = Carbon::parse($days['end_date']);
              // $holiday_count += $std->diffInDays($edd)+1;
              if($std==$edd){
                $holiday_count += 1;
              }else{
                $holiday_count += ($std->diffInDays($edd))+1;
              }
            } 
            $totaldays_excholidays = $totaldays_sofarthismonth-$holiday_count;
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]*$totaldays_excholidays));
          }elseif($vb==2){
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/7)*$totaldays_sofarthismonth);
          }elseif($vb==3){
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/$totaldays_sofarthismonth)*$totaldays_sofarthismonth);
          }
          $tp++;
        }
      }else{
        $salesmantgt_dates['tg_rule'] = ['a'];
        $salesmantgt_dates['tg_interval'] = ['a'];
        $salesmantgt_dates['tg_values'] = ['a'];
        $salesmantgt_dates['results_currentmonth'] = ['a'=>'b']; 
        $salesmantgt_dates['results_lastmonth'] = ['a'=>'b'];
        $salesmantgt_dates['results_yesterday'] = ['a'=>'b'];
        $salesmantgt_dates['results_today'] = ['a'=>'b'];
        $salesmantgt_dates['tg_values_calculated_thismonth'] = ['a'=>'b'];
      }
    }


    foreach($employee_withtargets as $indx=>$emptgt){
      $salesmantgt_hist = $emptgt['targetsalesmanassignhistory']->toArray();
      $salesmantgt = array();
      if(count($salesmantgt_hist)>0){
        $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
        foreach($salesmantgt_hist as $aa=>$bb){
          if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
            $salesmantgt[] = $bb; 
          }
        }
      } 

      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule_prevmonth'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval_prevmonth'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values_prevmonth'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_order_prev);
              break;
            case 2:
              $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->sum('grand_total');
              $allamt_orders_prev = $amt_order_prev;
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($allamt_orders_prev);
              break;
            case 3:
              $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_collection_prev);
              break;
            case 4:
              $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->sum('payment_received');
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($amt_collection_prev);
              break;
            case 5:
              $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();  
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_visits_prev);   
              break; 
            case 6:
              $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($golder_calls_prev);
              break;
            case 7:
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($all_orders_prev);
              break;
          }
        } 
        //converting interval to current month equivalent
        $tp = 1;
        $aa = Carbon::Parse($tg_prev_startdate);
        $bb = Carbon::Parse($tg_prev_enddate);
        $totaldays_sofarthismonth_prev = $aa->diffInDays($bb)+1;
        foreach($salesmantgt_dates['tg_interval_prevmonth'] as $bv=>$vb){
          if($vb==1){
            // $ab = Carbon::parse($datestart)->format('Y-m');
            $holidays = Holiday::where('company_id',$companyID)->whereBetween('start_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->toArray();
            $holiday_count = 0;
            foreach($holidays as $h=>$days){
              $std = Carbon::parse($days['start_date']);
              $edd = Carbon::parse($days['end_date']);
              // $holiday_count += $std->diffInDays($edd)+1;
              if($std==$edd){
                $holiday_count += 1;
              }else{
                $holiday_count += ($std->diffInDays($edd))+1;
              }
            } 
            $totaldays_excholidays = $totaldays_sofarthismonth_prev-$holiday_count;
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)($salesmantgt_dates['tg_values_prevmonth'][$bv]*$totaldays_excholidays);
          }elseif($vb==2){
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values_prevmonth'][$bv]/7)*$totaldays_sofarthismonth_prev);
          }elseif($vb==3){
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values_prevmonth'][$bv]/$totaldays_sofarthismonth_prev)*$totaldays_sofarthismonth_prev);
          }
          $tp++;
        }
      }else{
        $salesmantgt_dates['tg_rule_prevmonth'] = ['a'];
        $salesmantgt_dates['tg_interval_prevmonth'] = ['a'];
        $salesmantgt_dates['tg_values_prevmonth'] = ['a'];
        $salesmantgt_dates['results_lastmonth'] = ['a'=>'b'];
        $salesmantgt_dates['tg_values_calculated_prevmonth'][] = ['a'=>'b'];
      }
    }
    $response = array("status" => true, "message" => "Success", "data" => $salesmantgt_dates);
    return json_encode($response);
  }


  public function weeksInMonth($numOfDaysInMont){  
    $daysInWeek = 7;
    $result = $numOfDaysInMont/$daysInWeek;
    $numberOfFullWeeks = floor($result);
    $numberOfRemaningDays = ($result - $numberOfFullWeeks)*7;
    return $numberOfFullWeeks;
  }


  public function reportFiltered2(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
    $employee_withtargets = array();
    $salesmanid = ($request->input('id'))?($request->input('id')):'a';
    if($salesmanid!='a'){
      $saleman_exist = Employee::where('company_id',$companyID)->where('id',$salesmanid)->get();
      if(count($saleman_exist)==0){
        $response = array("status" => true, "message" => "Error, User not Found.", "data" => null);
        return json_encode($response);
      }
    }else{
      $response = array("status" => true, "message" => "Error, Request without user.", "data" => null);
      return json_encode($response);
    }
    $arrange_targets = $salesmantgt_dates = array();
    $tg_startdate =  ($request->startdate)?($request->startdate):(Carbon::now()->format('Y-m'));
    $curmonth = 'mm';
    if(empty($tg_startdate)){
      $chk_date = Carbon::now()->startOfMonth()->format('Y-m');
      $datestart_thismonth = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
      $dateend_thismonth = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
    }else{   
      $chk_date = $tg_startdate;
      $datestart_thismonth = $chk_date.'-01 00:00:00';
      $date = new DateTime();
      $dateend_thismonth = $date->modify("last day of $chk_date")->format("Y-m-d").' 23:59:59';
      $st1 = Carbon::parse($chk_date);
      $st2 = Carbon::parse(Carbon::now()->format('Y-m'));
      $diffDays = $st2->diffInDays($st1);
      if($diffDays==0){
        $curmonth = 'dd';
        $datestart_thismonth = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
        $dateend_thismonth = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
      }
    }
    $chk_date = $tg_startdate;
    $tt = explode('-',$chk_date);
    $total_monthdays = cal_days_in_month(CAL_GREGORIAN,$tt[1],$tt[0]);

    $prepQuery = Employee::where('company_id', $companyID)->where('status', '=', 'Active')->where('id',$salesmanid); 
    if($curmonth=='dd'){
        $prepQuery = $prepQuery->with(['targetsalesmanassign' => function($q) use($chk_date){
          $q->where('target_startmonth','like',$chk_date.'%');
        }]);
    }else{
        $prepQuery = $prepQuery->with(['targetsalesmanassignhistory' => function($q) use($chk_date){
          $q->where('target_startmonth','like',$chk_date.'%')->orderby('target_assigneddate','desc');
        }]);
    }
    $prepQuery = $prepQuery->get();

    $salesmantgt = array();
    foreach($prepQuery as $indx=>$emptgt){
      if($curmonth=='dd'){
        $salesmantgt = $emptgt['targetsalesmanassign']->toArray();
      }else{
        $salesmantgt_hist = $emptgt['targetsalesmanassignhistory']->toArray();
        if(count($salesmantgt_hist)>0){
          $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
          foreach($salesmantgt_hist as $aa=>$bb){
            if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
              $salesmantgt[] = $bb; 
            }
          }
        }
      }

      $salesmantgt_dates = array();
      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $tot_noorder_thismonth = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
            case 2:
              $amt_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('grand_total');
              $amt_noorder_thismonth = 0;
              $allamt_orders_thismonth = $amt_order_thismonth+$amt_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($allamt_orders_thismonth);
              break;
            case 3:
              $tot_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_collection_thismonth);
              break;
            case 4:
              $amt_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('payment_received');
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($amt_collection_thismonth);
              break;
            case 5:
              $tot_visits_thismonth = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->where('employee_id',$salesmanid)->get()->count();              
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_visits_thismonth);  
              break; 
            case 6:
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($golder_calls);
              break;
            case 7:
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $tot_noorder_thismonth = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
          }
          //converting interval to current month equivalent
          if(count($salesmantgt_dates)>0){
            foreach($salesmantgt_dates['tg_interval'] as $bv=>$vb){
              if($vb==1){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_targetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$chk_date,$total_monthdays));
              }elseif($vb==2){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_targetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$chk_date,$total_monthdays));
              }elseif($vb==3){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_targetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$chk_date,$total_monthdays));
              }
            }
          }
        } 
      }else{
        $salesmantgt_dates['tg_rule'] = ['a'];
        $salesmantgt_dates['tg_interval'] = ['a'];
        $salesmantgt_dates['tg_values'] = ['a'];
        $salesmantgt_dates['results_currentmonth'] = ['a'=>'b']; 
        $salesmantgt_dates['tg_values_calculated_thismonth'] = ['a'=>'b'];
      }
    }
    $response = array("status" => true, "message" => "Success", "data" => $salesmantgt_dates);
    return json_encode($response);
  }


  public function convert_targetvalues($company_id,$intervalid,$targetvalues,$act_date,$total_monthdays){
    $holidays = Holiday::where('company_id',$company_id)->where('start_date','like',$act_date.'%')->get()->toArray();
    $holiday_count = 0;
    foreach($holidays as $h=>$days){
      $std = Carbon::parse($days['start_date']);
      $edd = Carbon::parse($days['end_date']);
      if($std==$edd){
        $holiday_count += 1;
      }else{
        $holiday_count += ($std->diffInDays($edd))+1;
      }
    } 
    $total_workingdays = $total_monthdays-$holiday_count;
    switch($intervalid){
      case 1:
        $targetfor_wholemonth = $targetvalues*$total_workingdays;
        return $targetfor_wholemonth;
        break;
      case 2:
        $targetfor_wholemonth = ($targetvalues/7)*$total_monthdays;
        return $targetfor_wholemonth;
        break;
      case 3:
        $targetfor_wholemonth = ($targetvalues/$total_monthdays)*$total_monthdays;
        return $targetfor_wholemonth;
        break;
    }
  }


  public function npIndex(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
    $employee_withtargets = array();
    $salesmanid = ($request->input('id'))?($request->input('id')):'a';
    if($salesmanid!='a'){
      $saleman_exist = Employee::where('company_id',$companyID)->where('id',$salesmanid)->get();
      if(count($saleman_exist)==0){
        $response = array("status" => true, "message" => "Error, User not Found.", "data" => null);
        return json_encode($response);
      }
    }else{
      $response = array("status" => true, "message" => "Error, Request without user.", "data" => null);
      return json_encode($response);
    }
    $arrange_targets = $salesmantgt_dates = array();

    $date = new DateTime();
    $dateend_prev = $date->modify("last day of previous month")->format("Y-m-d");
    $tg_npstartdate = ($request->startdate)?($request->startdate):(getDeltaDate(Carbon::now()->startOfMonth()->format('Y-m-d')));
    $tg_npenddate = ($request->enddate)?($request->enddate):(getDeltaDate(Carbon::now()->endOfMonth()->format('Y-m-d')));
    $tg_npprev_startdate = ($request->startdate_prevmonth)?($request->startdate_prevmonth):(getDeltaDate(Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d')));
    $tg_npprev_enddate = ($request->endate_prevmonth)?($request->endate_prevmonth):(getDeltaDate($dateend_prev));
    
    $tg_startdate = nepToEngConvert($tg_npstartdate);
    $tg_enddate = nepToEngConvert($tg_npenddate);
    $tg_prev_startdate = nepToEngConvert($tg_npprev_startdate);
    $tg_prev_enddate = nepToEngConvert($tg_npprev_enddate);
    $tg_todaydate = Carbon::now()->format('Y-m-d');


    $tgenddate = explode('-',$tg_npenddate);
    $total_monthdays = $tgenddate[2];

    $tgcurmonth_chk = Carbon::Parse($tg_startdate)->format('Y-m');
    $tgprevmonth_chk = Carbon::Parse($tg_prev_startdate)->format('Y-m');
    $employee_withtargets = Employee::where('company_id', $companyID)->where('status', '=', 'Active')->where('id',$salesmanid); 
    $employee_withtargets = $employee_withtargets->with(['targetsalesmanassign' => function($q) use($tgcurmonth_chk){
      $q->where('target_startmonth','like',$tgcurmonth_chk.'%');
    }]);
    $employee_withtargets = $employee_withtargets->with(['targetsalesmanassignhistory' => function($q) use($tgprevmonth_chk){
      $q->where('target_startmonth','like',$tgprevmonth_chk.'%')->orderby('target_assigneddate','desc');
    }]);
    $employee_withtargets = $employee_withtargets->get();

    $salesmantgt = $salesmantgt_hist = array(); 
    foreach($employee_withtargets as $indx=>$emptgt){
      $salesmantgt = $emptgt['targetsalesmanassign']->toArray();

      $salesmantgt_dates = array();
      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart)->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->count();
                $all_orders_prev = $tot_order_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
                $all_orders_prev = $tot_order_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);

              }
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $tot_noorder_thismonth = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
            case 2:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($allamt_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev)->get()->sum('grand_total');
                $allamt_orders_prev = $amt_order_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($allamt_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($allamt_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->sum('grand_total');
                $allamt_orders_prev = $amt_order_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($allamt_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $amt_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->sum('grand_total');
                $amt_noorder = 0;
                $allamt_orders = $amt_order+$amt_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($allamt_orders);

                // $salesmantgt_dates['results'][$order_rule] = round($allamt_orders,2);
              }
              $amt_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->sum('grand_total');
              $amt_noorder_thismonth = 0;
              $allamt_orders_thismonth = $amt_order_thismonth+$amt_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($allamt_orders_thismonth);
              break;
            case 3:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart)->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_collection);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_collection);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_collection);

                // $salesmantgt_dates['results'][$order_rule] = round($tot_collection,2);
              }
              $tot_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_collection_thismonth);
              break;
            case 4:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart.'%')->get()->sum('payment_received');
                $salesmantgt_dates['results_today'][$order_rule] = (int)($amt_collection);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('payment_date','like',$datestart_prev)->get()->sum('payment_received');
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($amt_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($amt_collection);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_prev,$dateend_prev])->get()->sum('payment_received');
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($amt_collection_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $amt_collection = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart,$dateend])->get()->sum('payment_received');
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($amt_collection);
                // $salesmantgt_dates['results'][$order_rule] = round($amt_collection,2);
              }
              $amt_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_startdate,$tg_todaydate])->get()->sum('payment_received');
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($amt_collection_thismonth);
              break;
            case 5:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($tot_visits);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev)->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($tot_visits_prev);            
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();      
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($tot_visits);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($tot_visits_prev);        
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_visits = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();  
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($tot_visits);        
                // $salesmantgt_dates['results'][$order_rule] = round($tot_visits,2);
              }
              $tot_visits_thismonth = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_startdate,$tg_todaydate])->where('employee_id',$salesmanid)->get()->count();              
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_visits_thismonth);
              break; 
            case 6:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart.'%')->get()->count();
                $salesmantgt_dates['results_today'][$order_rule] = (int)($golder_calls);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->where('created_at','like',$datestart_prev.'%')->get()->count();
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($golder_calls_prev);   
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d').' 00:00:00';
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d').' 23:59:59';
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($golder_calls);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d').' 00:00:00';
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d').' 23:59:59';
                $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_prev,$dateend_prev])->get()->count();
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($golder_calls_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart,$dateend])->get()->count();
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($golder_calls);
                // $salesmantgt_dates['results'][$order_rule] = round($golder_calls,2);
              }
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$tg_startdate,$tg_todaydate])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($golder_calls);
              break;
            case 7:
              if($salesmantgt[$aa]['target_interval']==1){
                $datestart = Carbon::now()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart.'%')->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart.'%')->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_today'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::yesterday()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('order_date','like',$datestart_prev.'%')->get()->count();
                $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->where('date','like',$datestart_prev.'%')->get()->count();
                $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
                $salesmantgt_dates['results_yesterday'][$order_rule] = (int)($all_orders_prev);  
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==2){
                $datestart = Carbon::now()->startOfWeek()->format('Y-m-d');
                $dateend = Carbon::now()->endOfWeek()->format('Y-m-d');
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_currentweek'][$order_rule] = (int)($all_orders);

                $datestart_prev = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
                $dateend_prev = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
                $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_prev,$dateend_prev])->get()->count();
                $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_prev,$dateend_prev])->get()->count();
                $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
                $salesmantgt_dates['results_lastweek'][$order_rule] = (int)($all_orders_prev);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }elseif($salesmantgt[$aa]['target_interval']==3){
                $datestart = $tg_startdate;
                $dateend = $tg_enddate;
                $tot_order = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart,$dateend])->get()->count();
                $tot_noorder = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart,$dateend])->get()->count();
                $all_orders = $tot_order+$tot_noorder;
                $salesmantgt_dates['results_thismonth'][$order_rule] = (int)($all_orders);
                // $salesmantgt_dates['results'][$order_rule] = round($all_orders,2);
              }
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_startdate,$tg_todaydate])->get()->count();
              $tot_noorder_thismonth = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_startdate,$tg_todaydate])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
          }
        } 
        //converting interval to current month equivalent
        $tp = 1;
        $aa = Carbon::Parse($tg_startdate);
        $bb = Carbon::Parse($tg_enddate);
        $totaldays_sofarthismonth = $aa->diffInDays($bb)+1;
        foreach($salesmantgt_dates['tg_interval'] as $bv=>$vb){
          if($vb==1){
            // $ab = Carbon::parse($datestart)->format('Y-m');
            $holidays = Holiday::where('company_id',$companyID)->whereBetween('start_date',[$tg_startdate,$tg_enddate])->get()->toArray();
            $holiday_count = 0;
            foreach($holidays as $h=>$days){
              $std = Carbon::parse($days['start_date']);
              $edd = Carbon::parse($days['end_date']);
              // $holiday_count += $std->diffInDays($edd)+1;
              if($std==$edd){
                $holiday_count += 1;
              }else{
                $holiday_count += ($std->diffInDays($edd))+1;
              }
            } 
            $totaldays_excholidays = $totaldays_sofarthismonth-$holiday_count;
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]*$totaldays_excholidays));
          }elseif($vb==2){
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/7)*$totaldays_sofarthismonth);
          }elseif($vb==3){
            $salesmantgt_dates['tg_values_calculated_thismonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values'][$bv]/$total_monthdays)*$totaldays_sofarthismonth);
          }
          $tp++;
        }
      }else{
        $salesmantgt_dates['tg_rule'] = ['a'];
        $salesmantgt_dates['tg_interval'] = ['a'];
        $salesmantgt_dates['tg_values'] = ['a'];
        $salesmantgt_dates['results_currentmonth'] = ['a'=>'b']; 
        $salesmantgt_dates['results_lastmonth'] = ['a'=>'b'];
        $salesmantgt_dates['results_yesterday'] = ['a'=>'b'];
        $salesmantgt_dates['results_today'] = ['a'=>'b'];
        $salesmantgt_dates['tg_values_calculated_thismonth'] = ['a'=>'b'];
      }
    }


    foreach($employee_withtargets as $indx=>$emptgt){
      $salesmantgt_hist = $emptgt['targetsalesmanassignhistory']->toArray();
      $salesmantgt = array();
      if(count($salesmantgt_hist)>0){
        $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
        foreach($salesmantgt_hist as $aa=>$bb){
          if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
            $salesmantgt[] = $bb; 
          }
        }
      } 

      // print_r($salesmantgt);die();
      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule_prevmonth'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval_prevmonth'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values_prevmonth'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_order_prev);
              break;
            case 2:
              $amt_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->sum('grand_total');
              $allamt_orders_prev = $amt_order_prev;
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($allamt_orders_prev);
              break;
            case 3:
              $tot_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_collection_prev);
              break;
            case 4:
              $amt_collection_prev = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->sum('payment_received');
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($amt_collection_prev);
              break;
            case 5:
              $tot_visits_prev = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();  
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($tot_visits_prev);   
              break; 
            case 6:
              $golder_calls_prev = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($golder_calls_prev);
              break;
            case 7:
              $tot_order_prev = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $tot_noorder_prev = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$tg_prev_startdate,$tg_prev_enddate])->get()->count();
              $all_orders_prev = $tot_order_prev+$tot_noorder_prev;
              $salesmantgt_dates['results_lastmonth'][$order_rule] = (int)($all_orders_prev);
              break;
          }
        } 
        //converting interval to current month equivalent
        $tp = 1;
        $aa = Carbon::Parse($tg_startdate);
        $bb = Carbon::Parse($tg_enddate);
        $totaldays_sofarthismonth = $aa->diffInDays($bb)+1;
        foreach($salesmantgt_dates['tg_interval_prevmonth'] as $bv=>$vb){
          if($vb==1){
            // $ab = Carbon::parse($datestart)->format('Y-m');
            $holidays = Holiday::where('company_id',$companyID)->whereBetween('start_date',[$tg_prev_startdate,$tg_prev_enddate])->get()->toArray();
            $holiday_count = 0;
            foreach($holidays as $h=>$days){
              $std = Carbon::parse($days['start_date']);
              $edd = Carbon::parse($days['end_date']);
              // $holiday_count += $std->diffInDays($edd)+1;
              if($std==$edd){
                $holiday_count += 1;
              }else{
                $holiday_count += ($std->diffInDays($edd))+1;
              }
            } 
            $totaldays_excholidays = $totaldays_sofarthismonth-$holiday_count;
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)($salesmantgt_dates['tg_values_prevmonth'][$bv]*$totaldays_excholidays);
          }elseif($vb==2){
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values_prevmonth'][$bv]/7)*$totaldays_sofarthismonth);
          }elseif($vb==3){
            $salesmantgt_dates['tg_values_calculated_prevmonth'][$tp.'_'.$vb] = (int)(($salesmantgt_dates['tg_values_prevmonth'][$bv]/$total_monthdays)*$totaldays_sofarthismonth);
          }
          $tp++;
        }
      }else{
        $salesmantgt_dates['tg_rule_prevmonth'] = ['a'];
        $salesmantgt_dates['tg_interval_prevmonth'] = ['a'];
        $salesmantgt_dates['tg_values_prevmonth'] = ['a'];
        $salesmantgt_dates['results_lastmonth'] = ['a'=>'b'];
        $salesmantgt_dates['tg_values_calculated_prevmonth'][] = ['a'=>'b'];
      }
    }

    $response = array("status" => true, "message" => "Success", "data" => $salesmantgt_dates);
    return json_encode($response);
  }


  public function npReportFiltered2(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $data['months'] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
    $employee_withtargets = array();
    $salesmanid = ($request->input('id'))?($request->input('id')):'a';
    if($salesmanid!='a'){
      $saleman_exist = Employee::where('company_id',$companyID)->where('id',$salesmanid)->get();
      if(count($saleman_exist)==0){
        $response = array("status" => true, "message" => "Error, User not Found.", "data" => null);
        return json_encode($response);
      }
    }else{
      $response = array("status" => true, "message" => "Error, Request without user.", "data" => null);
      return json_encode($response);
    }
    $arrange_targets = $salesmantgt_dates = array();
    $tg_startdate_np = ($request->startdate)?($request->startdate):(getDeltaDate(Carbon::now()->startOfMonth()->format('Y-m-d')));
    $tg_enddate_np = ($request->enddate)?($request->enddate):(getDeltaDate(Carbon::now()->endOfMonth()->format('Y-m-d')));
    $curmonth = 'mm';
    if(empty($tg_startdate_np)){
      $chk_date = Carbon::now()->startOfMonth()->format('Y-m');
      $datestart_thismonth = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
      $dateend_thismonth = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
    }else{   
      $tg_startdate = nepToEngConvert($tg_startdate_np);
      $tg_enddate = nepToEngConvert($tg_enddate_np);
      $chk_date = Carbon::parse($tg_startdate)->format('Y-m');
      $datestart_thismonth = $tg_startdate.' 00:00:00';
      $dateend_thismonth = $tg_enddate.' 23:59:59';
      $st1 = Carbon::parse($tg_startdate)->format('Y-m');
      $st2 = Carbon::parse(Carbon::now()->format('Y-m'));
      $diffDays = $st2->diffInDays($st1);
      if($diffDays==0){
        $curmonth = 'dd';
      }
    }

    $tt = explode('-',$chk_date);
    $total_monthdays = cal_days_in_month(CAL_GREGORIAN,$tt[1],$tt[0]);

    $prepQuery = Employee::where('company_id', $companyID)->where('status', '=', 'Active')->where('id',$salesmanid); 
    if($curmonth=='dd'){
        $prepQuery = $prepQuery->with(['targetsalesmanassign' => function($q) use($chk_date){
          $q->where('target_startmonth','like',$chk_date.'%');
        }]);
    }else{
        $prepQuery = $prepQuery->with(['targetsalesmanassignhistory' => function($q) use($chk_date){
          $q->where('target_startmonth','like',$chk_date.'%')->orderby('target_assigneddate','desc');
        }]);
    }
    $prepQuery = $prepQuery->get();

    $salesmantgt = array();
    foreach($prepQuery as $indx=>$emptgt){
      if($curmonth=='dd'){
        $salesmantgt = $emptgt['targetsalesmanassign']->toArray();
      }else{
        $salesmantgt_hist = $emptgt['targetsalesmanassignhistory']->toArray();
        if(count($salesmantgt_hist)>0){
          $targetgroupid = $salesmantgt_hist[0]['target_groupid'];
          foreach($salesmantgt_hist as $aa=>$bb){
            if($targetgroupid==$salesmantgt_hist[$aa]['target_groupid']){
              $salesmantgt[] = $bb; 
            }
          }
        }
      }
      $salesmantgt_dates = array();
      if(count($salesmantgt)>0){
        foreach($salesmantgt as $aa=>$bb){
          $salesmantgt_dates['tg_rule'][] = $salesmantgt[$aa]['target_rule'];
          $salesmantgt_dates['tg_interval'][] = $salesmantgt[$aa]['target_interval'];
          $salesmantgt_dates['tg_values'][] = $salesmantgt[$aa]['target_values'];
          
          $order_rule = $salesmantgt[$aa]['target_rule'];
          switch($order_rule){
            case 1:
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $tot_noorder_thismonth = 0;//NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
            case 2:
              $amt_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('grand_total');
              $amt_noorder_thismonth = 0;
              $allamt_orders_thismonth = $amt_order_thismonth+$amt_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($allamt_orders_thismonth);
              break;
            case 3:
              $tot_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_collection_thismonth);
              break;
            case 4:
              $amt_collection_thismonth = Collection::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('payment_date',[$datestart_thismonth,$dateend_thismonth])->get()->sum('payment_received');
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($amt_collection_thismonth);
              break;
            case 5:
              $tot_visits_thismonth = ClientVisit::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->where('employee_id',$salesmanid)->get()->count();              
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($tot_visits_thismonth);  
              break; 
            case 6:
              $golder_calls = Client::where('company_id',$companyID)->where('created_by',$salesmanid)->whereBetween('created_at',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($golder_calls);
              break;
            case 7:
              $tot_order_thismonth = Order::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('order_date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $tot_noorder_thismonth = NoOrder::where('company_id',$companyID)->where('employee_id',$salesmanid)->whereBetween('date',[$datestart_thismonth,$dateend_thismonth])->get()->count();
              $all_orders_thismonth = $tot_order_thismonth+$tot_noorder_thismonth;
              $salesmantgt_dates['results_currentmonth'][$order_rule] = (int)($all_orders_thismonth);
              break;
          }
          //converting interval to current month equivalent
          if(count($salesmantgt_dates)>0){
            foreach($salesmantgt_dates['tg_interval'] as $bv=>$vb){
              if($vb==1){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_nptargetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$datestart_thismonth,$dateend_thismonth,$total_monthdays));
              }elseif($vb==2){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_nptargetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$datestart_thismonth,$dateend_thismonth,$total_monthdays));
              }elseif($vb==3){
                $salesmantgt_dates['tg_values_calculated_thismonth'][$order_rule] = (int)($this->convert_nptargetvalues($companyID,$vb,$salesmantgt_dates['tg_values'][$bv],$datestart_thismonth,$dateend_thismonth,$total_monthdays));
              }
            }
          }
        } 
      }else{
        $salesmantgt_dates['tg_rule'] = ['a'];
        $salesmantgt_dates['tg_interval'] = ['a'];
        $salesmantgt_dates['tg_values'] = ['a'];
        $salesmantgt_dates['results_currentmonth'] = ['a'=>'b']; 
        $salesmantgt_dates['tg_values_calculated_thismonth'] = ['a'=>'b'];
      }
    }
    $response = array("status" => true, "message" => "Success", "data" => $salesmantgt_dates);
    return json_encode($response);
  }


  public function convert_nptargetvalues($company_id,$intervalid,$targetvalues,$startdate,$enddate,$total_monthdays){
    $startdate = Carbon::parse($startdate)->format('Y-m-d');
    $enddate = Carbon::parse($enddate)->format('Y-m-d');
    $holidays = Holiday::where('company_id',$company_id)->whereBetween('start_date',[$startdate,$enddate])->get()->toArray();;
    $holiday_count = 0;
    foreach($holidays as $h=>$days){
      $std = Carbon::parse($days['start_date']);
      $edd = Carbon::parse($days['end_date']);
      if($std==$edd){
        $holiday_count += 1;
      }else{
        $holiday_count += ($std->diffInDays($edd))+1;
      }
    } 
    $total_workingdays = $total_monthdays-$holiday_count;
    switch($intervalid){
      case 1:
        $targetfor_wholemonth = $targetvalues*$total_workingdays;
        return $targetfor_wholemonth;
        break;
      case 2:
        $targetfor_wholemonth = ($targetvalues/7)*$total_monthdays;
        return $targetfor_wholemonth;
        break;
      case 3:
        $targetfor_wholemonth = ($targetvalues/$total_monthdays)*$total_monthdays;
        return $targetfor_wholemonth;
        break;
    }
  }



}