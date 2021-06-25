<?php

namespace App\Http\Controllers\Company\Admin;

use App\Client;
use App\Collection;
use App\Http\Controllers\Controller;
use App\Order;
use App\PartyType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use DB;
use Log;

class AgeingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ageing-view');
    }

    public function index(){
        $company_id = config('settings.company_id');
        if(config('settings.ageing')==1 && Auth::user()->can('ageing-view') && !getCompanyPartyTypeLevel(config('settings.company_id')) && config('settings.order_with_amt') ==0 && config('settings.accounting')==1 && Auth::user()->can('Accounting-view')){
          $empId = Auth::user()->EmployeeId();
          $partyTypes = PartyType::select('id','name')->where('company_id',$company_id)->get();
          $parties = Client::select('id','company_name')->where('company_id',$company_id)->where('status','Active');
          if(!(Auth::user()->isCompanyManager())){
            $handles = DB::table('handles')->where('company_id',$company_id)->where('employee_id',$empId)->pluck('client_id');
            $parties = $parties->whereIn('clients.id',$handles);
          }
          $parties = $parties->get();
          $partiesCount = $parties->count();
          return view('company.reportsageing.allparties',compact('partyTypes','parties','partiesCount'));
        }
        return redirect()->back();
    }

    public function ajaxDataTable(Request $request)
    {
        $columns = array( 
            0 => 'id', 
            1 => 'company_name',
            2 => 'current',
            3 => 'before30days',
            4 => 'due31to60days',
            5 => 'due61to90days',
            6 => 'over90days',
            7 => 'total',
        );
        $empId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
        $totalData = Client::where('company_id',$company_id)->where('status','Active')->count();
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        
        $todayDate = Carbon::now();

        $clients = Client::select('clients.id','clients.company_id','clients.client_type','clients.company_name','partytypes.id as partytype_id','partytypes.name as partytype_name')
                    ->leftJoin('partytypes','clients.client_type','partytypes.id')
                    ->where('clients.company_id',$company_id)->where('clients.status','Active');

        if(!(Auth::user()->isCompanyManager())){
            $handles = DB::table('handles')->where('company_id',$company_id)->where('employee_id',$empId)->pluck('client_id');
            $clients = $clients->whereIn('clients.id',$handles);
        }

        if($request->partyType){
            $clients = $clients->where('partytypes.id',$request->partyType);
        }
        if($request->party){
            $clients = $clients->where('clients.id',$request->party);
        }
        if($request->input('search.value')){
            $clients = $clients->where(function($q3)use($search){
              $q3 = $q3->where('clients.id','LIKE',"%{$search}%")
                    ->orWhere('clients.company_name','LIKE',"%{$search}%");
            });
        }

        $totalFiltered = $clients->get()->count();
        $clients =  $clients
                    ->orderBy($order,$dir)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
        $data = array();
        if(!empty($clients))
        {   
            $i = $start;
            foreach ($clients as $client)
            {
                $nestedData['id'] = ++$i;
                $nestedData['company_name'] = $client->company_name;
                $collectionPaymentAmount = Collection::where('company_id',$company_id)->where('payment_status','Cleared')->where('client_id',$client->id)->sum('payment_received');
                $over90days = $this->getDues($client->id,$company_id,'over90days');
                if($collectionPaymentAmount>$over90days){
                    $collectionPaymentAmount = $collectionPaymentAmount-$over90days;
                    $over90days = '-';
                }elseif($over90days>$collectionPaymentAmount && $over90days!=0){
                    $over90days = $over90days - $collectionPaymentAmount;
                    $collectionPaymentAmount = 0;
                }else{
                    $over90days = '-';
                    $collectionPaymentAmount = 0;
                }
                $nestedData['over90days'] = ($over90days!='-')?number_format($over90days,2):$over90days;

                $due61to90days = $this->getDues($client->id,$company_id,'61to90days');
                if($collectionPaymentAmount>$due61to90days){
                    $collectionPaymentAmount = $collectionPaymentAmount-$due61to90days;
                    $due61to90days = '-';
                }elseif($due61to90days>$collectionPaymentAmount && $due61to90days!=0){
                    $due61to90days = $due61to90days - $collectionPaymentAmount;
                    $collectionPaymentAmount = 0;
                }else{
                    $due61to90days = '-';
                    $collectionPaymentAmount = 0;
                }
                $nestedData['due61to90days'] = ($due61to90days!='-')?number_format($due61to90days,2):$due61to90days;

                $due31to60days = $this->getDues($client->id,$company_id,'31to60days');
                if($collectionPaymentAmount>$due31to60days){
                    $collectionPaymentAmount = $collectionPaymentAmount-$due31to60days;
                    $due31to60days = '-';
                }elseif($due31to60days>$collectionPaymentAmount && $due31to60days!=0){
                    $due31to60days = $due31to60days - $collectionPaymentAmount;
                    $collectionPaymentAmount = 0;
                }else{
                    $due31to60days = '-';
                    $collectionPaymentAmount = 0;
                }
                $nestedData['due31to60days'] = ($due31to60days!='-')?number_format($due31to60days,2):$due31to60days;

                $before30days = $this->getDues($client->id,$company_id,'before30days');
                if($collectionPaymentAmount>$before30days){
                    $collectionPaymentAmount = $collectionPaymentAmount-$before30days;
                    $before30days = '-';
                }elseif($before30days>$collectionPaymentAmount && $before30days!=0){
                    $before30days = $before30days - $collectionPaymentAmount;
                    $collectionPaymentAmount = 0;
                }else{
                    $before30days = '-';
                    $collectionPaymentAmount = 0;
                }
                $nestedData['before30days'] = ($before30days!='-')?number_format($before30days,2):$before30days;
                
                $current = $this->getDues($client->id,$company_id,'current');
                if($collectionPaymentAmount>$current){
                    $collectionPaymentAmount = $collectionPaymentAmount-$current;
                    $current = '-';
                }elseif($current>$collectionPaymentAmount && $current!=0){
                    $current = $current - $collectionPaymentAmount;
                    $collectionPaymentAmount = 0;
                }else{
                    $current = '-';
                    $collectionPaymentAmount = 0;
                }
                $nestedData['current'] = ($current!='-')?number_format($current,2):$current;
                $nestedData['total'] = number_format((($over90days!='-')?$over90days:0)
                                        +(($due61to90days!='-')?$due61to90days:0)
                                        +(($due31to60days!='-')?$due31to60days:0)
                                        +(($before30days!='-')?$before30days:0)
                                        +(($current!='-')?$current:0),2);
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

    private function getDues($clientID,$company_id,$type)
    {
        $ordersDueAmt = Order::select('orders.*','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                        ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                        ->where('orders.company_id',$company_id)
                        ->where('order_amt_flag',1);
        if($type=='current'){
            $ordersDueAmt = $ordersDueAmt->where('due_date','>=',Carbon::now()->format('Y-m-d'));
        }elseif($type=='before30days'){
            $ordersDueAmt = $ordersDueAmt->where('due_date','<',Carbon::now()->format('Y-m-d'))->where('due_date','>=',Carbon::now()->subDays(30)->format('Y-m-d'));
        }elseif($type=="31to60days"){
            $ordersDueAmt = $ordersDueAmt->where('due_date','<',Carbon::now()->subDays(30)->format('Y-m-d'))->where('due_date','>=',Carbon::now()->subDays(60)->format('Y-m-d'));
        }elseif($type=="61to90days"){
            $ordersDueAmt = $ordersDueAmt->where('due_date','<',Carbon::now()->subDays(60)->format('Y-m-d'))->where('due_date','>=',Carbon::now()->subDays(90)->format('Y-m-d'));
        }elseif($type=="over90days"){
            $ordersDueAmt = $ordersDueAmt->where('due_date','<',Carbon::now()->subDays(90)->format('Y-m-d'));
        }
        $ordersDueAmt=$ordersDueAmt->where('client_id',$clientID)->sum('grand_total');
        return $ordersDueAmt;
    }

    public function custompdfdexport(Request $request)
    {
        $getExportData = json_decode($request->exportedData)->data;
        $totalover90days = 0;
        $totaldue61to90days = 0;
        $totaldue31to60days = 0;
        $totalbefore30days = 0;
        $totalcurrent = 0;
        $totalAmount = 0;
        foreach($getExportData as $expData){
          $totalover90days      = $totalover90days + floatval(preg_replace('/[^\d.]/', '', $expData->over90days));
          $totaldue61to90days   = $totaldue61to90days + floatval(preg_replace('/[^\d.]/', '', $expData->due61to90days));
          $totaldue31to60days   = $totaldue31to60days + floatval(preg_replace('/[^\d.]/', '', $expData->due31to60days));
          $totalbefore30days    = $totalbefore30days + floatval(preg_replace('/[^\d.]/', '', $expData->before30days));
          $totalcurrent         = $totalcurrent + floatval(preg_replace('/[^\d.]/', '', $expData->current));
          $totalAmount          = $totalAmount + floatval(preg_replace('/[^\d.]/', '', $expData->total));
        }
        $pageTitle = $request->pageTitle;
        set_time_limit(300);
        ini_set("memory_limit", "256M");
        $pdf = PDF::loadView('company.reportsageing.exportpdf', compact('getExportData', 'pageTitle','totalover90days','totaldue61to90days','totaldue31to60days','totalbefore30days','totalcurrent','totalAmount'))->setPaper('a4', 'portrait');
        $download = $pdf->download($pageTitle.'.pdf');
        return $download;
    }

    // Breakdown Report Section Begins
    public function indexBreakdown(){
        $company_id = config('settings.company_id');
        if(config('settings.ageing')==1 && Auth::user()->can('ageing-view') && !getCompanyPartyTypeLevel(config('settings.company_id')) && config('settings.order_with_amt') ==0 && config('settings.accounting')==1 && Auth::user()->can('Accounting-view')){
          $today = Carbon::now();
          $empId = Auth::user()->EmployeeId();
          $parties = Client::select('id','company_name')->where('company_id',$company_id)->where('status','Active');
          if(!(Auth::user()->isCompanyManager())){
              $handles = DB::table('handles')->where('company_id',$company_id)->where('employee_id',$empId)->pluck('client_id');
              $parties = $parties->whereIn('clients.id',$handles);
          }
          $parties = $parties->get();
          $ordersCount = Order::select('orders.id','orders.company_id','orders.client_id','orders.order_no','orders.order_date','orders.due_date','orders.delivery_status_id','clients.company_name as party','orders.grand_total','clients.id as clientID','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                      ->leftJoin('clients','orders.client_id','clients.id')
                      ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                      ->where('orders.due_date','<',$today->format('Y-m-d'))
                      ->where('orders.company_id',$company_id)
                      ->where('order_amt_flag',1)->get()->count();
          return view('company.reportsbreakdownageing.allparties',compact('parties','ordersCount'));
        }
        return redirect()->back();
    }

    public function ajaxBreakdownDatatable(Request $request)
    {
        $columns = array( 
            0 => 'id', 
            1 => 'party',
            2 => 'order_no',
            3 => 'amount',
            4 => 'overdueDays'
        );
        $company_id = config('settings.company_id');
        $empId = Auth::user()->EmployeeId();
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        
        $today = Carbon::now();
        if(!(Auth::user()->isCompanyManager())){
            $handles = DB::table('handles')->where('company_id',$company_id)->where('employee_id',$empId)->pluck('client_id');
            $clients = Client::select('id')->where('company_id',$company_id)->whereIn('id',$handles)->where('status','Active')->get();
        }else{
            $clients = Client::select('id')->where('company_id',$company_id)->where('status','Active')->get();
        }
        $orderIDs = [];
        $resultOrderIds = [];
        foreach($clients as $client){
            $orderIDs = $this->getUnPaidOrders($client->id,$orderIDs);
            $resultOrderIds = array_unique(array_merge($resultOrderIds,$orderIDs));
        }
        $totalData = count($resultOrderIds);

        $orders = Order::select('orders.id','orders.company_id','orders.client_id','orders.order_no','orders.order_date','orders.due_date','orders.delivery_status_id','clients.company_name as party','orders.grand_total','clients.id as clientID','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('clients','orders.client_id','clients.id')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.due_date','<',$today->format('Y-m-d'))
                    ->where('orders.company_id',$company_id)
                    ->where('order_amt_flag',1)
                    ->whereIn('orders.id',$resultOrderIds);
        if($request->party){
            $orders = $orders->where('clients.id',$request->party);
        }
        if($request->input('search.value')){
            $orders = $orders->where(function($q3)use($search){
              $q3 = $q3->where('orders.id','LIKE',"%{$search}%")
                    ->orWhere('clients.company_name','LIKE',"%{$search}%");
            });
        }

        $totalFiltered = $orders->get()->count();
        $orders =   $orders
                    ->orderBy($orderColumn,$dir)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
        $data = array();
        if(!empty($orders))
        {   
            $i = $start;
            $footerTotal = 0.0;
            foreach ($orders as $order)
            {
                $tempDueInfo = [];
                $nestedData['id'] = ++$i;
                $nestedData['party'] = $order->party;
                $nestedData['order_no'] = config('settings.order_prefix').$order->order_no;
                $tempDueInfo = $this->getBreakdownDues($order->id,$order->client_id,$company_id);
                $footerTotal = $footerTotal + $tempDueInfo['amount'];
                $nestedData['amount'] = number_format($tempDueInfo['amount'],2);
                $nestedData['overdueDays'] = $tempDueInfo['overdue_days'];
                $data[] = $nestedData;
            }
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "total"           => config('settings.currency_symbol').' '.number_format($footerTotal,2),  
                    "data"            => $data,
                    );
            
        echo json_encode($json_data); 
    }

    private function getBreakdownDues($orderId,$clientID,$company_id)
    {
        $today = Carbon::now();
        $collectionPaymentAmount = Collection::where('company_id',$company_id)->where('payment_status','Cleared')
                    ->where('client_id',$clientID)->sum('payment_received');
        $orders = Order::select('orders.id','orders.company_id','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('client_id',$clientID)
                    ->where('order_amt_flag',1)
                    ->where('due_date','<',$today->format('Y-m-d'))
                    ->orderBy('due_date','ASC')->get();
        foreach($orders as $order){
            if($order->grand_total<=$collectionPaymentAmount){
                $collectionPaymentAmount = $collectionPaymentAmount - $order->grand_total;
            }elseif($order->grand_total>$collectionPaymentAmount && $collectionPaymentAmount!=0){
                if($order->id==$orderId){
                    return ['amount'=>$order->grand_total-$collectionPaymentAmount,'overdue_days' =>Carbon::parse($order->due_date)->diffInDays($today)];
                }else{
                    $collectionPaymentAmount = 0;
                }
            }else{
                if($order->id==$orderId){
                    return ['amount'=>$order->grand_total,'overdue_days' =>Carbon::parse($order->due_date)->diffInDays($today)];
                }
            }
        }
        return ['amount'=>0,'overdue_days'=>0];
    }

    public function custombreakdownpdfdexport(Request $request)
    {
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $totalAmount = 0;
      foreach($getExportData as $expData){
        $totalAmount = $totalAmount + floatval(preg_replace('/[^\d.]/', '', $expData->amount));
      }
      set_time_limit(300);
      ini_set("memory_limit", "256M");
      $pdf = PDF::loadView('company.reportsbreakdownageing.exportpdf', compact('getExportData', 'pageTitle','totalAmount'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    private function getUnPaidOrders($clientID,$orderIDs){
        $company_id = config('settings.company_id');
        $today = Carbon::now();
        $ClientCollectionsPaymentSum = Collection::where('company_id',$company_id)->where('payment_status','Cleared')->where('client_id',$clientID)->sum('payment_received');
        $orders = Order::select('orders.id','orders.company_id','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('due_date','<',$today->format('Y-m-d'))
                    ->where('client_id',$clientID)
                    ->where('order_amt_flag',1)
                    ->orderBy('due_date','ASC')->get();
        foreach($orders as $order){
            if($order->grand_total<=$ClientCollectionsPaymentSum){
                $ClientCollectionsPaymentSum = $ClientCollectionsPaymentSum - $order->grand_total;
            }else{
                array_push($orderIDs,$order->id);
                $ClientCollectionsPaymentSum = 0;
            }
        }
        return $orderIDs;
    }
    // End Breakdown Report Section
}
