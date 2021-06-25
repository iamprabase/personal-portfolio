<?php

namespace App\Http\Controllers\Company\Admin;


use DB;
use Log;
use Auth;
use View;
use Excel;
use Session;
use Storage;
use App\Beat;
use DateTime;
use App\Brand;
use App\Order;
use App\Note;
use App\Stock;
use App\Client;
use DatePeriod;
use App\Manager;
use App\NoOrder;
use App\Category;
use App\Employee;
use App\Location;
use App\Product; 
use DateInterval;
use App\BeatVPlan;
use App\PartyType;
use App\UnitTypes;
use Carbon\Carbon;
use App\Attendance;
use App\Collection;
use App\Activity;
use App\ClientVisit;
use App\OrderDetails;
use App\ReturnDetail;
use App\ProductReturn;
use App\ClientSetting;
use App\GenerateReport;
use App\ProductVariant;
use App\ModuleAttribute;
use App\BeatPlansDetails;
use Illuminate\Http\Request;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ReportNewController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */

  public function __construct()
  {
    $this->middleware('auth');
    //set_time_limit(300);
    $this->middleware('permission:report-view');
  }

  function getVariants($product_id){
    $variants = Product::join('product_variants','product_variants.product_id','products.id')
                ->where('products.id', $product_id)
                ->get();

    return $variants;
  }

  public function updateReportGeneratedId(){
    $company_id = config('settings.company_id');
    
    $generatedReports = GenerateReport::where('company_id', $company_id);
    if($generatedReports->count()>0){
      $manager = Manager::where('company_id', $company_id)->first(['user_id']);
      $employeeInstance = Employee::where('company_id', $company_id)->where('user_id', $manager->user_id)->first(['id']);
      $generatedReports->update(['generated_by' => $employeeInstance->id]);
      die("DONE");
    }
    die("NOT DONE");
  }

  function fetchhandledparty(Request $request){
    $salesman_id = $request->sel_salesman;
    $handled_party = Client::join('handles','handles.client_id', 'clients.id')
                    ->where('handles.employee_id', $salesman_id)
                    ->select('handles.client_id', 'clients.company_name')
                    ->orderby('clients.company_name', 'asc')
                    ->get()
                    ->toJson();
    return $handled_party;
  }

  /*
    *** Today Attendance Report Redirected From Dashboard
  */

  public function todayattendancereport(Request $request)
  {
    $company_id = config('settings.company_id');
    $today = date('Y-m-d');
    $employees = Auth::user()->handleQuery('employee')->where('status', 'Active')->select('employees.name','employees.id')->get();
    $attendances = Employee::leftJoin('attendances', 'attendances.employee_id','employees.id')->where('attendances.company_id', $company_id)
                    ->where('attendances.adate', '=', $today)->where('check_type', 1)->distinct('attendances.employee_id')->get(['attendances.employee_id','employees.name', 'attendances.adate', 'attendances.check_type', 'attendances.atime', 'attendances.address']);

    if(empty($attendances)){
        $attendance['present_employees'] = 0;
        $attendance['absent_employees'] = 0;
    }else{
        $attendancesID = $attendances->pluck('employee_id')->toArray();
        $employeesID =  $employees->pluck('id')->toArray();
        $attendance['present_employees'] = $attendances->whereIn('employee_id',$employeesID )->unique('employee_id');
        $attendance['absent_employees'] = $employees->whereNotIn('id',$attendancesID);
    }
    return view('company.newreports.todayattendance', compact('attendance'));
  }

  /*
  *** Order Reports
  */
  public function orderreports(){
    
    if(config('settings.ordersreport')==0 || !(Auth::user()->can('orderr-view'))){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                    ->pluck('title', 'id')
                    ->toArray();
    
    $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')
                    ->toArray();
    return view('company.newreports.orderreportsnew', compact('partytypes', 'order_statuses'));                             
  }

  public function orderreportsDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'report_type',
      2 => 'date_range',
      3 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'report_type') $order = "report_type"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'Order')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData; 
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
      foreach ($generated_reports as $generated_report) {
          $nestedData = array();
          $decode_employees = json_decode($generated_report->employee_id);
          $decode_clients = json_decode($generated_report->party_id);
          $decode_beats = json_decode($generated_report->beat_id);
          $decode_order_status = json_decode($generated_report->delivery_status_id);

          if (isset($decode_employees)) {
              $tooltip_header = "<b>Salesmen:-</b>";
              if (count($decode_employees)==1) {
                  $employee_name = Employee::find($decode_employees[0])->name;
                  $report_type = $employee_name;
                  $tooltip_content = $tooltip_header.$employee_name;
              } else {
                  $employee_names = Employee::whereIn('id', $decode_employees)->pluck('name')->toArray();
                  $report_type = "Salesmen";
                  $contents = implode(',', $employee_names);
                  $tooltip_content = $tooltip_header.$contents;
              }
          } elseif (isset($decode_clients)) {
              $tooltip_header = "<b>Parties:-</b>";
              if (count($decode_clients)==1) {
                  $client_name = Client::find($decode_clients[0])->company_name;
                  $report_type = $client_name;
                  $tooltip_content = $tooltip_header.$client_name;
              } else {
                  $client_names = Client::whereIn('id', $decode_clients)->pluck('company_name')->toArray();
                  $report_type = "Parties";
                  $contents = implode(',', $client_names);
                  $tooltip_content = $tooltip_header.$contents;
              }
          } elseif (isset($decode_beats)) {
              $tooltip_header = "<b>Beats:-</b>";
              if (count($decode_beats)==1) {
                  $beat_name = Beat::find($decode_beats[0])->name;
                  $report_type = $beat_name;
                  $tooltip_content = $tooltip_header.$beat_name;
              } else {
                  $beat_names = Beat::whereIn('id', $decode_beats)->pluck('name')->toArray();
                  $report_type = "Beat-wise";
                  $contents = implode(',', $beat_names);
                  $tooltip_content = $tooltip_header.$contents;
              }
          } elseif (isset($decode_order_status)) {
              $tooltip_header = "<b>Order Status:-</b>";
              if (count($decode_order_status)==1) {
                  $order_status = $order_statuses[$decode_order_status[0]];
                  $report_type = $order_status;
                  $tooltip_content = $tooltip_header.$order_status;
              } else {
                  $contents = "";
                  foreach ($decode_order_status as $status) {
                      $contents .= $status .",";
                  }
                  $report_type = "Order Status - wise";
                  $tooltip_content = $tooltip_header.$contents;
              }
          } else {
              $tooltip_header = "<b>Beats:-</b>";
              $report_type = "Unspecified Beat";
              $tooltip_content = $tooltip_header."Unspecified Beat";
          }
          $generated_report->report_type = $report_type;
          $generated_report->tooltip_content = $tooltip_content;

          if(isset($generated_report->start_date) && isset($generated_report->end_date)){
            if($generated_report->start_date == $generated_report->end_date) $date_range = getDeltaDate($generated_report->start_date);
            else $date_range = getDeltaDate($generated_report->start_date) . " to " . getDeltaDate($generated_report->end_date);
          }else{
            $date_range = $generated_report->date_range;
          }

          if(!empty($generated_report->download_link)){
            $action = "<a href='{$generated_report->download_link}' id='download_button' download='{urldecode($generated_report->filename)}'><i class='fa fa-download' aria-hidden='true'></i></a>";
          }
          else{
            if($generated_report->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
            elseif($generated_report->processing==3) $action = "No Record";
            else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
          }
          $tooltip_content = "$generated_report->tooltip_content";
          $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="<b>The report was generated for following parties:-</b><br/><span>'.$tooltip_content.'</span>"></span>';
          $nestedData['date_generated'] = getDeltaDate($generated_report->created_at->format('Y-m-d'));
          $nestedData['report_type'] = $generated_report->report_type . "-- Order Report --" . $generated_report->report_cat . $spanContent;
          $nestedData['date_range'] = $date_range;
          $nestedData['action'] = $action;
          
          $data[] = $nestedData;
      }
    }
    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }

  private function getBrandCategoryUnitMapping($products, $brandCollec, $categoriesCollec, $unitsCollec){
    $products->map(function($item) use($categoriesCollec, $brandCollec, $unitsCollec){
      $item['brand_name'] = "Unspecified";
      $item['category_name'] = "Unspecified";
      $item['unit_symbol'] = null;
      $filterBrandName = (empty($item->brand))?($item['brand_name'] =  "Unspecified"):$brandCollec->filter(function ($brand) use($item) {
                            if($item->brand == $brand->id){
                              $item['brand_name'] =  $brand->name;
                              return true;
                            }
                          });
      $filterCategoryName = (empty($item->category_id))?($item['category_name'] =  "Unspecified"):$categoriesCollec->filter(function ($category) use($item) {
                              if($item->category_id == $category->id){
                                $item['category_name'] =  $category->name;
                                return true;
                              }
                            });
      $filterUnitName = (empty($item->variant_unit))?$unitsCollec->filter(function ($unit) use($item) {
                            if($item->product_unit == $unit->id){
                              $item['unit_symbol'] =  $unit->symbol;
                              return true;
                            }
                          }):$unitsCollec->filter(function ($unit) use($item) {
                            if($item->variant_unit == $unit->id){
                              $item['unit_symbol'] =  $unit->symbol;
                              return true;
                            }
                          });
      return $item;
    });
    return $products;
  }

  private function getProductColletion($companyProductVariants, $companyOrders){
    $newCollection = collect();
    $prevObj = null;

    foreach($companyProductVariants as $product){
      if($product->product_status=="Inactive"){
        $checkInactiveAndOrdered = (clone $companyOrders)->where('product_id', $product->product_id)->count();
        if($checkInactiveAndOrdered==0) continue;
      }
      if(isset($product->variant_id)){
        if(isset($prevObj)){
          if($prevObj->product_id!=$product->product_id){
            $checkIfOrderExistBeforeVariant = array_unique((clone $companyOrders)->where('product_id', $product->product_id)->where('product_variant_id', NULL)->where('unit', '!=', NULL)->pluck('unit')->toArray());
            if(count($checkIfOrderExistBeforeVariant)>0){
              foreach($checkIfOrderExistBeforeVariant as $key=>$orderUnit){
                if(isset($orderUnit) && $orderUnit!=$product->product_unit){
                  $cloned = (clone $product);
                  $cloned->variant_id = NULL;
                  $cloned->variant = "";
                  $cloned->product_unit = $orderUnit;
                  $cloned->variant_unit = $orderUnit;
                  $newCollection->push($cloned);
                  unset($cloned);
                }
              }
            }
          }
        }
        $getOrderDetails = array_unique((clone $companyOrders)->where('product_id', $product->product_id)->where('product_variant_id', $product->variant_id)->where('unit','!=', NULL)->pluck('unit')->toArray());
        if(count($getOrderDetails)>0){
          foreach($getOrderDetails as $orderUnit){
            if($orderUnit!=$product->variant_unit && isset($orderUnit)){
              $cloned = (clone $product);
              $cloned->variant_unit = $orderUnit;
              $newCollection->push($cloned);
            }
          }
        }
      }else{
        $getOrderDetails = array_unique((clone $companyOrders)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->pluck('unit')->toArray());
        if(count($getOrderDetails)>0){
          foreach($getOrderDetails as $orderUnit){
            if($orderUnit!=$product->product_unit && isset($orderUnit)){
              $cloned = (clone $product);
              $cloned->product_unit = $orderUnit;
              $newCollection->push($cloned);
            }
          }
        }
      }
      unset($prevObj);
      $prevObj = $product;
      $newCollection->push($product);
    }

    return $newCollection;
  }

  public function generateorderreports(Request $request){
    $customMessages = [
      'party_salesman.required' => 'Field is required',
      'emp_party_sel.required' => 'Field is required',
      'start_date.required' => 'Field is required',
      'end_date.required' => 'Field is required',
      'start_date.date' => 'Invalid date format.',
      'end_date.date' => 'Invalid date format.',

    ];
    $this->validate($request, [
      'start_date' => 'required|date:Y-m-d', 
      'end_date' => 'required|date:Y-m-d',
      'party_salesman' => 'required',
      'emp_party_sel' => 'required'
    ], $customMessages);
    $company_id = config('settings.company_id');
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    if($request->party_salesman == 'beat_wise'){
      $beat_lists = $request->emp_party_sel;
      $reportObjects = Beat::with('clients')->whereStatus('Active')->whereIn('id', $beat_lists)->orderBy('name', 'ASC')->get(['id', 'name as name']);
      $checkUnspecified = in_array(0, $beat_lists);
      $handledClients = DB::table('handles')->where('employee_id', Auth::user()->EmployeeId())->pluck('client_id')->toArray();
      $client_with_beats = DB::table('beat_client')->whereIn('beat_id', $beat_lists)->whereIn('client_id', $handledClients)->pluck('client_id')->toArray();

      $allClients = Client::where('company_id', $company_id)->whereIn('id', $handledClients)->pluck('id')->toArray();
      $clientWithBeats = DB::table('beat_client')->whereIn('client_id', $allClients)->pluck('client_id')->toArray();
      $clientWithNoBeats = array_diff($allClients, $clientWithBeats);
      $client_with_no_beats = Client::where('company_id', $company_id)->whereIn('id', $clientWithNoBeats)->pluck('id')->toArray();
      $emp_party_sel = array_merge($client_with_beats, $client_with_no_beats);
      
      if(!$checkUnspecified){
        $checkifBeatOrderExists = Order::whereIn('client_id', $client_with_beats)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
      }elseif($checkUnspecified && count($beat_lists)==1){
        $checkifBeatOrderExists = Order::whereIn('client_id', $client_with_no_beats)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
      }elseif($checkUnspecified && count($beat_lists)>1){
        $checkifBeatOrderExists = Order::whereIn('client_id', $emp_party_sel)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
      }
      
      if($checkifBeatOrderExists<1){
        return redirect()->route('company.admin.variantreports', ['domain' => domain()])->with('error_message', "There are no orders between selected dates.");
      }
    }elseif($request->party_salesman == 'custom_salesman'){
      $reportObjects = Employee::whereHas('orders', function($query) use ($startDate, $endDate) { 
        $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        })->whereIn('id', $request->emp_party_sel)->orderBy('name', 'ASC')->get(['id', 'name']);
      
    }elseif($request->party_salesman == 'custom_party'){
      $reportObjects = Client::whereHas('orders', function($query) use ($startDate, $endDate) {
        $query->whereBetween('orders.order_date', [$startDate, $endDate]);
          })->whereIn('clients.id', $request->emp_party_sel)->orderBy('company_name', 'ASC')->get(['id', 'company_name as name', 'location']);
    }elseif($request->party_salesman == 'order_status_wise'){
      $reportObjects = ModuleAttribute::whereCompanyId($company_id)
                      ->whereIn('id', $request->emp_party_sel)
                      ->get(['title as name', 'id']);
    }

    if($reportObjects->count() == 0){
      return redirect()->route('company.admin.variantreports', ['domain' => domain()])->with('error_message', "There are no orders between selected dates.");
    }

    $reportdata=array(
      'company_id' => $company_id,
      'type' => 'xlsx',
      'today' => date('Y-m-d'),
      'report_type' => $request->report_type,
      'party_salesman' => $request->party_salesman,
      'emp_party_sel' => $request->emp_party_sel,
      'startDate' => $request->start_date,
      'endDate' => $request->end_date,
    );

    $report = new GenerateReport;
    $report->company_id = $company_id;
    $report->report_type = 'Order';
    $report->which_report = $request->party_salesman;
    $report->order_status = json_encode($request->order_status);
    if($request->report_type == "brand"){
      $report_cat = 'By Brand';
    }elseif($request->report_type == "category"){
      $report_cat = 'By Category';
    }elseif($request->report_type == "consolidated"){
      $report_cat = 'Consolidated';
    }
    $report->report_cat = $report_cat;
    if($request->party_salesman == "custom_salesman"){
      $report->employee_id = json_encode($request->emp_party_sel);
      $report->party_id = null;
    }elseif($request->party_salesman == "custom_party"){
      $report->party_id = json_encode($request->emp_party_sel);
      $report->employee_id = null;
      }elseif($request->party_salesman == "beat_wise"){
        $report->beat_id =json_encode($request->emp_party_sel);
        $report->employee_id = null;
    }elseif($request->party_salesman == "order_status_wise"){
      $report->delivery_status_id = json_encode($request->order_status);
    }
    $report->start_date = $request->start_date;
    $report->end_date = $request->end_date;
    $report->generated_by = Auth::user()->EmployeeId();
    $report->save();
    //\Artisan::call("order:report");
    if($report){
    
      return redirect()->route('company.admin.variantreports', ['domain' => domain()])->with('successful_message', 'Your Report will be generated soon.');
    }else{
      return redirect()->route('company.admin.variantreports', ['domain' => domain()])->with('error_message', 'There was some problem generating report.Please try again.');
    }
  }

  public function getAttendanceDetails($attendanceCollection, $salesmanId){
    $attendanceCheckInDetails = $attendanceCollection->filter(function($attendance) use($salesmanId){
      if($attendance->employee_id == $salesmanId && $attendance->check_type == 1)
        return true;
    });

    $attendanceCheckOutDetails = $attendanceCollection->filter(function($attendance) use($salesmanId){
      if($attendance->employee_id == $salesmanId && $attendance->check_type == 2)
        return true;
    });

    $attendanceDetails['CheckIn'] = $attendanceCheckInDetails->first();
    $attendanceDetails['CheckOut'] = $attendanceCheckOutDetails->last();
    return $attendanceDetails;
  }

  public function getLastOrderPlacedDate($clientId, $allOrdersCollection){
    if(!empty($allOrdersCollection)){
      $lastDate = $allOrdersCollection->where('client_id', $clientId)->max('order_date');
    }else{
      $lastDate = "";
    }

    return $lastDate?getDeltaDateForReport($lastDate):$lastDate;
  }

  function getLastOrderPlacedDateAndClient($clientIds, $allOrdersCollection){
    $lastOrderClientDate = array('client'=>"", 'date'=>"");
    if(!empty($allOrdersCollection)){
      $lastDate = $allOrdersCollection->whereIn('client_id', $clientIds)->sortByDesc('id')->first();
      if(isset($lastDate)){
        $id = $lastDate->client_id;
        $lastOrderClientDate["client"] = Client::find($id)->company_name;
        $lastOrderClientDate["date"] = $lastDate->order_date;  
      }
    }

    return $lastOrderClientDate;
  }

  public function getZeroOrdersCount($clientId, $zeroOrdersCollection){
    if(!empty($zeroOrdersCollection)){
      $numZeroOrders = $zeroOrdersCollection->where('client_id', $clientId)->toArray();

      $count = count($numZeroOrders);
    }else{
      $count = 0;
    }

    return $count;
  }

  function getZeroOrdersCountForBeats($clientIds, $zeroOrdersCollection){
    if(!empty($zeroOrdersCollection)){
      $numZeroOrders = $zeroOrdersCollection->whereIn('client_id', $clientIds)->toArray();

      $count = count($numZeroOrders);
    }else{
      $count = 0;
    }

    return $count;
  }
  
  public function getEmployeePendingAndTotalCalls($employeeId, $orderCollection, $noOrderCollection, $beatPlansCollection){
    $callsData = array();
    $totalCalls = array();
    $pendingCalls = array();

    $clientsWithOrders = $orderCollection->where('employee_id', $employeeId)->unique('client_id')->pluck('client_id')->toArray();
    
    $clientsWithNoOrders = $noOrderCollection->where('employee_id', $employeeId)->unique('client_id')->whereNotIn('client_id',$clientsWithOrders)->pluck('cient_id')->toArray();

    $totalCalls = array_merge($clientsWithOrders, $clientsWithNoOrders);
    if(!empty($beatPlansCollection)){
      $clientsVisited = $beatPlansCollection->where('employee_id', $employeeId)->pluck('client_id')->toArray();
      foreach($clientsVisited as $client){
        $clients = explode(',',$client);
        foreach($clients as $client_id){
          if(!in_array($pendingCalls, $clients)){
            array_push($pendingCalls, $client_id);
          }
        }
      }
    }

    $callsData['totalCalls'] = $totalCalls;
    $callsData['pendingCalls'] = $pendingCalls; 
    return $callsData;
  }

  public function getOrderSumBeforeAddingVariant($key, $searchId, $productCollection, $orderCollection){
    $orders = $orderCollection->whereIn($key, $searchId)->where('product_id', $productCollection->product_id)->where('product_variant_id', null)->where('unit', $productCollection->product_unit);

    if(empty($orders))
      return null;

    return $orders; 
  }

  public function getOrdersDetails($key, $searchId, $productCollection, $orderCollection){
    if($productCollection->variant_flag == 0){
      $orderSum = $orderCollection->where($key, $searchId)->where('product_id', $productCollection->product_id)->where('product_variant_id', null)->where('unit', $productCollection->product_unit)->sum('quantity');
    }elseif($productCollection->variant_flag == 1){
      $orderSum = $orderCollection->where($key, $searchId)->where('product_id', $productCollection->product_id)->where('product_variant_id', $productCollection->variant_id)->where('unit', $productCollection->variant_unit)->sum('quantity');
    } 

    return $orderSum;
  }

  public function getInactiveProductCount($orderCollection, $productId){
    $count = $orderCollection->where('product_id',$productId)->count();
    return $count; 
  }

  /*
  *** Product Sales Order Report
  */

  public function productsalesreports(){
    if(config('settings.psoreport')==0 || !(Auth::user()->can('psor-view'))){
      return redirect()->back();
    }
    //set_time_limit(500);
    $company_id = config('settings.company_id');
    $parties = Auth::user()->handleQuery('client')->select('id','company_name','client_type')->orderBy('company_name','asc')->get()->toJson();
    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
    $salesman = Auth::user()->handleQuery('employee')->select('id','name')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();
    
    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                    ->pluck('title', 'id')
                    ->toArray();

    return view('company.newreports.productsales',compact('salesman','partytypes','parties','order_statuses'));
  }    

  public function generateproductsalesreports(Request $request){
    $company_id = config('settings.company_id');
    $customMessages = [
      'salesman_ids.required' => 'Field is required',
      'party_ids.required' => 'Field is required',
      'order_status_select.required' => 'Field is required',
      'start_date.required' => 'Field is required',
      'end_date.required' => 'Field is required',
      'start_date.date' => 'Invalid date format.',
      'end_date.date' => 'Invalid date format.',

    ];
    $this->validate($request, [
      'start_date' => 'required|date:Y-m-d', 
      'end_date' => 'required|date:Y-m-d',
      'salesman_ids' => 'required',
      'party_ids' => 'required',
      'order_status_select' => 'required'
    ], $customMessages);
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    // $dates = $request->dates;
    $selected_statuses = $request->order_status_select;
    $type = 'xlsx';
    // if($startDate == $endDate){
    //   if(config('settings.ncal')==0){
    //     $date_head = getDeltaDateForReport($startDate);  
    //     $file_date = getDeltaDateForReport($startDate);
    //   }else{
    //     $date_head = getDeltaDateForReport($startDate);
    //     $file_date = getDeltaDateForReport($startDate);
    //   }
    // }else{
    //   if(config('settings.ncal')==0){
    //     $date_head = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);  
    //     $file_date = getDeltaDateForReport($startDate).' to '.getDeltaDateForReport($endDate);
    //   }else{
    //     $date_head = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
    //     $file_date = getDeltaDateForReport($startDate).' to '.getDeltaDateForReport($endDate);
    //   }
    // }
    $salesman_ids = empty($request->salesman_ids)?[]:$request->salesman_ids;
    $party_ids = empty($request->party_ids)?[]:$request->party_ids;
    $report_type = $request->report_type;
    // $approvedId = ModuleAttribute::where('company_id', $company_id)->where('title', 'Approved')->first()->id;
    $orders = Order:://join('orderproducts', 'orderproducts.order_id', 'orders.id')
                where('orders.company_id', $company_id)
                ->whereBetween('orders.order_date', [$startDate, $endDate])
                ->whereIn('orders.delivery_status_id', $selected_statuses)
                ->where(function($query) use($salesman_ids, $party_ids){
                  $query->whereIn('orders.employee_id', $salesman_ids);
                  $query->whereIn('orders.client_id', $party_ids);                                
                })->whereNull('orders.deleted_at')
                ->count();
    if($orders<=0){
      Session::flash('warning', 'There are no records between selected dates.');
      return back();
      // return response()->json(['msg' => "There are no records between selected dates.",'path' => NULL, 'status' => 203]);
    }
    // $brands = Brand::where('company_id', $company_id)
    //             ->orderby('name', 'ASC')
    //             ->get(['id', 'name']);
    // $categories = Category::where('company_id', $company_id)
    //               ->orderby('name', 'ASC')
    //               ->get(['id', 'name']);
    // $units = DB::table('unit_types')->where('company_id', $company_id)
    //           ->orderby('name', 'ASC')
    //           ->get(['id', 'symbol']);
    
    // $companyProductVariants = Product::where('products.company_id', $company_id)
    //                             ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
    //                             ->orderBy('products.category_id', 'desc')
    //                             ->orderBy('products.id', 'desc')
    //                             ->get(['products.id as product_id','products.product_name', 'products.brand','products.category_id','products.unit as product_unit', 'products.status as product_status', 'products.variant_flag', 'product_variants.id as variant_id', 'product_variants.variant', 'product_variants.unit as variant_unit']);
    // $newCollec = collect();
    // $prevObj = null;
    // foreach($companyProductVariants as $product){
    //   if($product->product_status=="Inactive"){
    //     $checkInactiveAndOrdered = (clone $orders)->where('product_id', $product->product_id)->count();
    //     if($checkInactiveAndOrdered==0) continue;
    //   }
    //   if(isset($product->variant_id)){
    //     if(isset($prevObj)){
    //       if($prevObj->product_id!=$product->product_id){
    //         $checkIfOrderExistBeforeVariant = array_unique((clone $orders)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->pluck('unit')->toArray());
    //         if(count($checkIfOrderExistBeforeVariant)>0){
    //           foreach($checkIfOrderExistBeforeVariant as $key=>$orderUnit){
    //             if(isset($orderUnit) && $orderUnit!=$product->product_unit){
    //               $cloned = (clone $product);
    //               $cloned->variant_id = NULL;
    //               $cloned->variant = "";
    //               $cloned->product_unit = $orderUnit;
    //               $cloned->variant_unit = $orderUnit;
    //               $newCollec->push($cloned);
    //               unset($cloned);
    //             }
    //           }
    //         }
    //       }
    //     }
    //     $getOrderDetails = array_unique((clone $orders)->where('product_id', $product->product_id)->where('product_variant_id', $product->variant_id)->where('unit','!=', NULL)->pluck('unit')->toArray());
    //     if(count($getOrderDetails)>0){
    //       foreach($getOrderDetails as $orderUnit){
    //         if($orderUnit!=$product->variant_unit && isset($orderUnit)){
    //           $cloned = (clone $product);
    //           $cloned->variant_unit = $orderUnit;
    //           $newCollec->push($cloned);
    //         }
    //       }
    //     }
    //   }else{
    //     $getOrderDetails = array_unique((clone $orders)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->pluck('unit')->toArray());
    //     if(count($getOrderDetails)>0){
    //       foreach($getOrderDetails as $orderUnit){
    //         if($orderUnit!=$product->product_unit && isset($orderUnit)){
    //           $cloned = (clone $product);
    //           $cloned->product_unit = $orderUnit;
    //           $newCollec->push($cloned);
    //         }
    //       }
    //     }
    //   }
    //   unset($prevObj);
    //   $prevObj = $product;
    //   $newCollec->push($product);
    // }

    // unset($companyProductVariants);
    // $companyProductVariants = $newCollec;
    // $companyProducts = $companyProductVariants
    //                     ->map(function($item) use($categories, $brands, $units){
    //                       $item['brand_name'] = "Unspecified";
    //                       $item['category_name'] = "Unspecified";
    //                       $item['unit_symbol'] = null;
    //                       $filterBrandName = (empty($item->brand))?($item['brand_name'] =  "Unspecified"):$brands->filter(function ($brand) use($item) {
    //                                             if($item->brand == $brand->id){
    //                                               $item['brand_name'] =  $brand->name;
    //                                               return true;
    //                                             }
    //                                           });
    //                       $filterCategoryName = (empty($item->category_id))?($item['category_name'] =  "Unspecified"):$categories->filter(function ($category) use($item) {
    //                                               if($item->category_id == $category->id){
    //                                                 $item['category_name'] =  $category->name;
    //                                                 return true;
    //                                               }
    //                                             });
    //                       $filterUnitName = (empty($item->variant_unit))?$units->filter(function ($unit) use($item) {
    //                                             if($item->product_unit == $unit->id){
    //                                               $item['unit_symbol'] =  $unit->symbol;
    //                                               return true;
    //                                             }
    //                                           }):$units->filter(function ($unit) use($item) {
    //                                             if($item->variant_unit == $unit->id){
    //                                               $item['unit_symbol'] =  $unit->symbol;
    //                                               return true;
    //                                             }
    //                                           });
    //                       return $item;
    //                     })->sortBy('product_name');
    // unset($companyProductVariants);

    // $data = array();                    
    // $prod_header = array("Product Name","Variant","Category","Brand","Unit","Total (in selected period)");
    // $grand_total = array("Grand-Total", "", "", "", "", 0);
    // $getCalendarSetting = config('settings.ncal');
    // $headerFlag = true;
    // if($report_type==30){
    //   $grandTotal = 0;
    //   foreach($companyProducts as $product){
    //     $reportData = array();
    //     $reportData[] = $product->product_name;
    //     $reportData[] = $product->variant;
    //     $reportData[] = $product->category_name;
    //     $reportData[] = $product->brand_name;
    //     $reportData[] = $product->unit_symbol;
    //     $reportData[5] = "";

    //     $total_in_sel_period = 0;
  
    //     foreach($dates as $date){
    //       if(isset($product->variant_id))
    //         $getSumOfOrders = $this->getQuantitySum($orders, $date, $product->product_id, $product->variant_id, $product->variant_unit);
    //       else 
    //         $getSumOfOrders = $this->getQuantitySum($orders, $date, $product->product_id, $product->variant_id, $product->product_unit);
    //       if($headerFlag){
    //         if($getCalendarSetting==0){
    //           $formatted_date = date("jS F Y", strtotime($date));  
    //         }else{
    //           $formatted_date = getDeltaDateForReport($date);
    //         }
    //         array_push($prod_header,$formatted_date);
    //       }
    //       $total_in_sel_period += $getSumOfOrders;
          
    //       if(!isset($grand_total[$date])) $grand_total[$date] = strval($getSumOfOrders);
    //       elseif(isset($grand_total[$date])) $grand_total[$date] = strval((int)$grand_total[$date]+$getSumOfOrders);

    //       $grandTotal += $getSumOfOrders;
          
    //       array_push($reportData, "$getSumOfOrders");
    //       unset($getSumOfOrders);
    //     }

        
    //     if ($headerFlag) {
    //       $headerFlag = false;
    //       $data[] = $prod_header;
    //     }
    //     $reportData[5] = "$total_in_sel_period";
    //     $data[] = $reportData; 
    //   }
    //   $grand_total[5] = "$grandTotal";
    //   $data[] = $grand_total;
    // }elseif($report_type==12){
    //   $grandTotal = 0;
    //   $loopDates = array();
    //   foreach($dates as $date){
    //     if($getCalendarSetting==0){
    //       $formatted_date = date("F Y", strtotime($date));
    //     }else{
    //       $formatted_date = getMthYrDeltaDate($date);
    //     }
    //     if(!in_array($formatted_date, $prod_header)){
    //       array_push($prod_header,$formatted_date);
    //     }
    //     $month = date("n", strtotime($date));
    //     $year = date("Y", strtotime($date));
    //     $loopDates[$month.'_'.$year][] = $date;
    //   }
    //   foreach($companyProducts as $product){
    //     $reportData = array();
    //     $reportData[] = $product->product_name;
    //     $reportData[] = $product->variant;
    //     $reportData[] = $product->category_name;
    //     $reportData[] = $product->brand_name;
    //     $reportData[] = $product->unit_symbol;
    //     $reportData[5] = "";

    //     $total_in_sel_period = 0;
  
    //     foreach($loopDates as $key=>$dates){
    //       if(isset($product->variant_id))
    //         $getSumOfOrders = $this->getMontlyQuantitySum((clone $orders), $dates, $product->product_id, $product->variant_id, $product->variant_unit);
    //       else
    //         $getSumOfOrders = $this->getMontlyQuantitySum((clone $orders), $dates, $product->product_id, $product->variant_id, $product->product_unit);

    //       $total_in_sel_period += $getSumOfOrders;
          
    //       if(!isset($grand_total[$key])) $grand_total[$key] = strval($getSumOfOrders);
    //       elseif(isset($grand_total[$key])) $grand_total[$key] = strval((int)$grand_total[$key]+$getSumOfOrders);

    //       $grandTotal += $getSumOfOrders;
          
    //       array_push($reportData, "$getSumOfOrders");
    //       unset($getSumOfOrders);
    //     }

        
    //     if ($headerFlag) {
    //       $headerFlag = false;
    //       $data[] = $prod_header;
    //     }
    //     $reportData[5] = "$total_in_sel_period";
    //     $data[] = $reportData; 
    //   }
    //   $grand_total[5] = "$grandTotal";
    //   $data[] = $grand_total;    
    // }

    
    // if($report_type==30){
    //   $sheet_title = "Daily_".$date_head;
    //   $filename_download = 'ProductSalesReport_'.$file_date.'(dly)'.'.xlsx';
    //   $filename = 'ProductSalesReport_'.$file_date.'(dly)_'.time();
    //   $report_type_name = "daily";
    // }elseif($report_type==12){
    //   $sheet_title = "Monthly_".$date_head;
    //   $filename_download = 'ProductSalesReport_'.$file_date.'(mthly)'.'.xlsx';
    //   $filename = 'ProductSalesReport_'.$file_date.'(mthly)_'.time();
    //   $report_type_name = "monthly";
    // }
    // $information[] = array();
    // $information[] = array("Parties", "","Salesmen");
    // if($salesman_ids!= null && $party_ids!=null){
    //   if(count($party_ids)>count($salesman_ids)){
    //     $len = count($party_ids);
    //     for($ind=0; $ind<$len; $ind++){
    //       $party_salsman = array();
    //       $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
    //       if($ind<count($salesman_ids)){
    //         $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
    //       }else{
    //         $salsman_name = NULL;
    //       }
    //       array_push($party_salsman,$prty_name, "", $salsman_name);
    //       unset($prty_name);
    //       unset($salsman_name);
    //       $information[] = $party_salsman;
    //       unset($party_salsman);
    //     }
    //   }elseif(count($party_ids)<=count($salesman_ids)){
    //     $len = count($salesman_ids);
    //     for($ind=0; $ind<$len; $ind++){
    //       $party_salsman = array();
    //       $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
    //       if($ind<count($party_ids)){
    //           $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
    //       }else{
    //           $prty_name = NULL;
    //       }
    //       array_push($party_salsman,$prty_name, "", $salsman_name);
    //       unset($prty_name);
    //       unset($salsman_name);
    //       $information[] = $party_salsman;
    //       unset($party_salsman);
    //     }
    //   }
    // }elseif($salesman_ids!= NULL && $party_ids==NULL){
    //   $len = count($salesman_ids);
    //   for($ind=0; $ind<$len; $ind++){
    //     $party_salsman = array();
    //     $prty_name = NULL;
    //     $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
    //     array_push($party_salsman,$prty_name, "", $salsman_name);
    //     unset($prty_name);
    //     unset($salsman_name);
    //     $information[] = $party_salsman;
    //     unset($party_salsman);
    //   }
    // }elseif($salesman_ids== NULL && $party_ids!=NULL){
    //   $len = count($party_ids);
    //   for($ind=0; $ind<$len; $ind++){
    //     $party_salsman = array();
    //     $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
    //     $salsman_name = NULL;
    //     array_push($party_salsman,$prty_name, "", $salsman_name);
    //     unset($prty_name);
    //     unset($salsman_name);
    //     $information[] = $party_salsman;
    //     unset($party_salsman);
    //   }
    // }else{
    //   $party_salsman = array();
    //   $prty_name = "None";
    //   $salsman_name = "None";
    //   array_push($party_salsman,$prty_name, "", $salsman_name);
    //   unset($prty_name);
    //   unset($salsman_name);
    //   $information[] = $party_salsman;
    //   unset($party_salsman);
    // }
    // $excelExport["Information Sheet"] = $information;
    // $excelExport[$sheet_title] = $data;
    
    // $store = Excel::store(new MultiSheetExport($excelExport), $filename.'.'.$type, 'productSalesReports');

    // $path = 'http://' . $_SERVER['HTTP_HOST'] . '/cms/storage/excel/reports/productSalesReports/' . $filename . '.' . 'xlsx';
    $report = new GenerateReport;
    $report->company_id = $company_id;//config('settings.company_id');
    $report->filename = NULL;//$filename_download;
    $report->report_type = 'ProductSales';
    $report->report_cat = $report_type==30?'daily':'monthly';//$report_type_name;
    $report->party_id = !empty($party_ids)?json_encode($party_ids):NULL;
    $report->employee_id = !empty($salesman_ids)?json_encode($salesman_ids):NULL;
    $report->order_status = json_encode($selected_statuses);
    $report->start_date = $request->start_date;
    $report->end_date = $request->end_date;
    $report->generated_by = Auth::user()->EmployeeId();
    $report->download_link = NULL;//$path;
    $report->save();

    Session::flash('success', 'Your Report will be generated soon.');
    return back();
    // return response()->json(['msg' => "Generated.",'path' => NULL, 'status' => 200]);
  }

  public function productsalesreportsDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'report_type',
      2 => 'date_range',
      3 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'ProductSales')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){

        $cat = $reports->report_cat;
        $decode_party = json_decode($reports->party_id, true);
        $decode_salesman = json_decode($reports->employee_id, true);
        $employee_names = !empty($decode_salesman)?Employee::whereIn('id', $decode_salesman)->pluck('name')->toArray():"";
        $company_names = !empty($decode_party)?Client::whereIn('id', $decode_party)->pluck('name')->toArray():"";
        if(!empty($employee_names)) $salesmans = implode(',', $employee_names);
        else $salesmans = "";
        if(!empty($company_names)) $partie_companies = implode(',', $company_names);
        else $partie_companies = "";
        
        if($reports->employee_id!=NULL && $reports->party_id!=NULL){
          $content = "<b>The report was generated for following parties and salesman:-</b><br/><b>Parties</b>:-{$partie_companies}<br/><b>Salesmen:-</b>{$salesmans}";
        }elseif($reports->employee_id!=NULL && $reports->party_id==NULL){
          $content = "<b>The report was generated for following parties and salesman:-</b><br/><b>Salesmen:-</b>{$salesmans}<br/><b>Parties:- None</b>";
        }elseif($reports->employee_id==NULL && $reports->party_id!=NULL){
          $content = "<b>The report was generated for following parties and salesman:-</b><br/><b>Parties:-</b>{$partie_companies}<br/> <b>Salesman:-</b>None";
        }else{
          $content = "<b>Parties:-</b> None Selectced <br/> <b>Salesman:-</b> None Selected";
        }
        $reports->content = strtoupper($cat) ." " . $content;
        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date)
            $reports->date = getDeltaDateFormat($reports->start_date);
          else
            $reports->date = getDeltaDateFormat($reports->start_date). 'to' . getDeltaDateFormat($reports->end_date);
        } else{
          $reports->date = $reports->date_range;
        }

        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $tooltip_content = "$reports->tooltip_content";
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';

        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['report_type'] = strtoupper($reports->report_cat) . $spanContent;
        $nestedData['date_range'] = $date_range;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }
    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }

  private function getQuantitySum($orderCollection, $orderDate, $productId, $productVariantId, $unitId){
    if(isset($productVariantId)){
      $qtySum = $orderCollection->where('order_date', $orderDate)->where('product_id',$productId)
                              ->where('product_variant_id',$productVariantId)
                              ->where('unit',$unitId)
                              ->sum('quantity');
    }else{
      $qtySum = $orderCollection->where('order_date', $orderDate)->where('product_id',$productId)
                              ->where('product_variant_id', '=', null)
                              ->where('unit',$unitId)
                              ->sum('quantity');
    }

    return $qtySum;
  }

  private function getMontlyQuantitySum($orderCollection, $datesList, $productId, $productVariantId, $unitId){
    if(isset($productVariantId)){
      $qtySum = $orderCollection->whereIn('order_date', $datesList)->where('product_id',$productId)
                              ->where('product_variant_id',$productVariantId)
                              ->where('unit',$unitId)
                              ->sum('quantity');
    }else{
      $qtySum = $orderCollection->whereIn('order_date', $datesList)->where('product_id',$productId)
                              ->where('product_variant_id', '=', null)
                              ->where('unit',$unitId)
                              ->sum('quantity');
    }

    return $qtySum;
  }

  /*
  *** Salesman Party-wise Order Report
  */

  public function dailysalesmanreports(){
    if(config('settings.spwise')==0 || !(Auth::user()->can('spartywiser-view'))){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');
    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

    $salesman = Auth::user()->handleQuery('employee')->select('name', 'id')->where('company_id', $company_id)
                ->orderBy('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();
    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                    ->pluck('title', 'id')
                    ->toArray();

    return view('company.newreports.dailysalesmanreports', compact('salesman', 'order_statuses'));
  }

  public function generatedailysalesmanreports(Request $request){
    $company_id = config('settings.company_id');
    $type = 'xlsx';
    $salesman_id = $request->salesman_id;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $order_statuses = $request->order_status_select;
    $report_type = $request->report_type;
    if(empty($salesman_id) || empty($startDate) || empty($endDate) || empty($order_statuses) || empty($report_type)){
      // $path = NULL;
      $msg = "Please select necessary fields.";
      // return response()->json(['msg' => $msg,'path' => $path, 'status' => 203]);
      Session::flash("warning", $msg);
      return back();
    }
    $handled_party = Auth::user()->handleQuery('client')
                    ->pluck('id')->toArray();
                    
    $salesmanOrdersCount = Order::where('employee_id', $salesman_id)->whereBetween('orders.order_date', [$startDate, $endDate])
                          ->whereIn('orders.client_id', $handled_party)
                          ->whereIn('orders.delivery_status_id', $order_statuses)->count();
    if($salesmanOrdersCount==0){
      // $path = NULL;
      $msg = "There are no records for this salesman between selected dates.";
      // return response()->json(['msg' => $msg,'path' => $path, 'status' => 203]);
      Session::flash("warning", $msg);
      return back();
    }else{
      $msg = "Your Report will be generated soon."; 
      // $path = 'http://' . $_SERVER['HTTP_HOST'] . '/cms/storage/excel/reports/dailySalesmanReports/' . $after_download_name . '.' . 'xlsx';
      $report = new GenerateReport;
      $report->company_id = $company_id;
      $report->filename = NULL;//$filename_download.'.xlsx';
      $report->report_type = 'DailySalesman';
      $report->report_cat = $report_type;
      $report->party_id = NULL;
      $report->employee_id = $salesman_id;
      $report->order_status = json_encode($order_statuses);
      $report->start_date = $startDate;
      $report->end_date = $endDate;
      $report->generated_by = Auth::user()->EmployeeId();
      $report->download_link = NULL;//$path;
      $report->save();
      Session::flash("success", $msg);
      return back();
      // return response()->json(['msg' => $msg,'path' => NULL, 'status' => 200]);
    }
  }

  public function salesmanpartywisereportsDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'salesman_name', 
      2 => 'report_type',
      3 => 'date_range',
      4 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'salesman_name') $order = "employee_id"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'DailySalesman')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){
        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $employee = getEmployee($reports->employee_id);
        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['salesman_name'] = $employee?$employee->name:"";
        $nestedData['report_type'] = strtoupper($reports->report_cat);
        $nestedData['date_range'] = $date_range;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }

    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }

  /*
  *** Beat Report
  */

  public function beatroutereports(Request $request){
      if(config('settings.beat')==0 || !(Auth::user()->can('beatplanreport-view'))){
        return redirect()->back();
      }
      $company_id = config('settings.company_id');
      
      $salesman = Auth::user()->handleQuery('employee')->orderBy('name', 'asc')
                          ->pluck('name', 'id')
                          ->toArray();
      return view('company.newreports.beatroutereports', compact('salesman'));
  }

  public function datebeatroutereport(Request $request){

      $company_id = config('settings.company_id');
      $date = $request->startdate;

      $salesman = Auth::user()->handleQuery('employee')
                  ->select('id','name')
                  ->orderBy('name', 'asc')
                  ->get();
      $handled_employees = (clone $salesman)->pluck('id')->toArray();
      $beats = BeatVPlan::where('company_id', $company_id)
              ->join('beatplansdetails','beatplansdetails.beatvplan_id','beatvplans.id')
              ->whereIn('beatvplans.employee_id', $handled_employees)
              ->where('beatplansdetails.plandate', $date)
              ->get();
              
      $company_clients = Auth::user()->handleQuery('client')
                          ->orderby('company_name', 'asc')
                          ->get();

      $company_orders = Auth::user()->handleQuery('order')
                          ->where('order_date', $date)
                          ->orderby('client_id', 'asc')
                          ->get();

      $company_no_orders = Auth::user()->handleQuery('no_order')
                          ->where('date', $date)
                          ->orderby('client_id', 'asc')
                          ->get();
              
      foreach($salesman as $employee){

          $employee_id = $employee->id;
          $employee_beats = $beats->where('employee_id','=' ,$employee_id);
          

          if($employee_beats->first()){
              // Planned clients
              $clients = explode(',',$employee_beats->first()->client_id);
              
              // Target calls/visits
              $employee_target_visits = $company_clients
                                          ->whereIn('id',$clients)
                                          ->pluck('company_name', 'id')->toArray();

              $orders = $company_orders
                          ->where('employee_id', $employee_id);
  
              $no_orders = $company_no_orders
                              ->where('employee_id', $employee_id);
  
              $orders_clients = $orders->whereIn('client_id', $clients)->pluck('client_id')->toArray();
  
              $no_orders_clients = $no_orders->whereIn('client_id', $clients)->pluck('client_id')->toArray();

              // Effective calls
              $employee_effective_calls = $company_clients
                                          ->whereIn('id',$orders_clients)
                                          ->pluck('company_name','id')->toArray();
              
              // Non-Effective calls
              $employee_non_effective_calls = $company_clients
                                              ->whereIn('id',$no_orders_clients)
                                              ->pluck('company_name','id')->toArray();

                
              $party_added = $company_clients
                              ->where('created_by', $employee->id)
                              ->where('created_at', 'like', $date.'%')
                              ->pluck('id')
                              ->toArray();

              $unscheduled_effective_calls_clients = $orders
                                                      ->whereNotIn('client_id', $clients)
                                                      ->pluck('client_id')
                                                      ->toArray();
              // Unscheduled effective calls
              $unscheduled_effective_calls = $company_clients
                                              ->whereIn('id',$unscheduled_effective_calls_clients)
                                              ->pluck('company_name','id')
                                              ->toArray();

              $unscheduled_non_effective_calls_clients = $no_orders
                                                      ->whereNotIn('client_id', $clients)
                                                      ->pluck('client_id')
                                                      ->toArray();
              // Unscheduled non effective calls
              $unscheduled_non_effective_calls = $company_clients
                                                      ->whereIn('id',$unscheduled_non_effective_calls_clients)
                                                      ->pluck('company_name','id')
                                                      ->toArray();

              $employee_actual_visits_clients = array_unique(array_merge($orders_clients, $no_orders_clients)); 
              $unscheduled = array_unique(array_merge($unscheduled_effective_calls_clients, $unscheduled_non_effective_calls_clients));
              $actual_visits = array_unique(array_merge($unscheduled, $employee_actual_visits_clients));
              
              // Total visits with Planned clients
              $employee_actual_visits = $company_clients
                                          ->whereIn('id',$actual_visits)
                                          ->pluck('company_name','id')
                                          ->toArray();
              
              $not_covered_array = array_diff($clients,$employee_actual_visits_clients);
              // Not covered
              $not_covered = $company_clients
                                      ->whereIn('id',$not_covered_array)
                                      ->pluck('company_name', 'id')
                                      ->toArray();
  
              $beatIDS = explode(',',$employee_beats->first()->beat_id);
              $beat_names_lists = Beat::whereIn('id',$beatIDS)->orderby('name','asc')->get();
              $beat_names = array();
              foreach($beat_names_lists as $beat_names_list){
                  $parse_date = date('Y-m-d', strtotime($beat_names_list->updated_at));
                  
                  array_push($beat_names, $beat_names_list->name);
              }
              
              $beatsVisited = array();
              $clientsVisited = array();
              $beatClients = json_decode($employee_beats->first()->beat_clients);
              foreach($beatClients as $key=>$beatClient){
                  foreach($beatClient as $btClient){
                      if(in_array($btClient, $actual_visits)){
                          if(!(in_array($key, $beatsVisited))){
                              array_push($beatsVisited, $key);
                          }
                          array_push($clientsVisited, $btClient);
                      }
                  }
              }
              $diffInarray = array_diff($actual_visits,$clientsVisited);
              

              foreach($actual_visits as $actual_visit){
                  if(!(in_array((string)$actual_visit, $clientsVisited))){
                      $getBeatID = DB::table('beat_client')->where('client_id', $actual_visit)->first();
                      if($getBeatID){
                          $gtbtID = $getBeatID->beat_id;
                          array_push($beatsVisited, $gtbtID);
                      }
                  }
              }

              $actual_beats = Beat::whereIn('id',$beatsVisited)->pluck('name')->toArray();
              $actual_beats = Beat::whereIn('id',$beatsVisited)->pluck('name')->toArray();
              $beat_route[$employee_id] = array( $employee->name, $employee_target_visits, $employee_actual_visits, 
              $employee_effective_calls, $unscheduled_effective_calls, $employee_non_effective_calls, $unscheduled_non_effective_calls, $not_covered, $beat_names, $actual_beats);

              $clients_ids[$employee_id] = array(implode(',',array_unique(array_merge($orders_clients,$unscheduled_effective_calls_clients))), implode(',',array_unique(array_merge($no_orders_clients,$unscheduled_non_effective_calls_clients))), implode(',',array_unique($not_covered_array)));

              unset($clients);
              unset($employee_actual_visits_clients);
              unset($beat_names);
              unset($actual_beats);
          }
      }

      if(isset($beat_route)){
          return View::make('company.newreports.beatroute', compact('beat_route','clients_ids'))->render();
      }else{
          return View::make('company.newreports.beatroute')->render();
      }
  }

  public function salesmanbeatroutereport(Request $request){

      $company_id = config('settings.company_id');
      $fromdate = $request->fromdate;
      $todate = $request->todate;
      $emp_id = $request->emp_id;

      $salesman = Auth::user()->handleQuery('employee')
                  ->where('id', $emp_id)
                  ->first();
      $emp_name = $salesman->name;

      $beats = BeatVPlan::where('company_id', $company_id)
                ->join('beatplansdetails','beatplansdetails.beatvplan_id','beatvplans.id')
                ->whereBetween('beatplansdetails.plandate', [$fromdate, $todate])
                ->where('employee_id', $emp_id)
                ->orderby('beatplansdetails.plandate', 'desc')
                ->get();

      $company_clients = Auth::user()->handleQuery('client')
                          ->orderby('company_name', 'asc')
                          ->get();
      
      $orders = Auth::user()->handleQuery('order')
                ->where('employee_id', $emp_id)
                ->whereBetween('order_date', [$fromdate, $todate])
                ->orderBy('client_id', 'asc')
                ->get();

      $no_orders = Auth::user()->handleQuery('no_order')
                    ->where('employee_id', $emp_id)
                    ->whereBetween('date', [$fromdate, $todate])
                    ->orderBy('client_id', 'asc')
                    ->get();

      if($beats->first()){
          
          foreach($beats as $beat){
              $client_ids = explode(',',$beat->client_id);
              $beat_ids = explode(',',$beat->beat_id);
              $date = $beat->plandate;
              $emp[$date]['employee_target_visits'] = $company_clients
                                                          ->whereIn('id',$client_ids)
                                                          ->pluck('company_name','id')->toArray();

              $orders_clients = $orders->where('order_date', '=', $date)->whereIn('client_id', $client_ids)->pluck('client_id')->toArray();
  
              $no_orders_clients = $no_orders->where('date', '=', $date)->whereIn('client_id', $client_ids)->pluck('client_id')->toArray(); 

              $emp[$date]['employee_effective_calls'] = $company_clients
                                                          ->whereIn('id',$orders_clients)
                                                          ->pluck('company_name','id')->toArray();
              
              $emp[$date]['employee_non_effective_calls'] = $company_clients
                                                          ->whereIn('id',$no_orders_clients)
                                                          ->pluck('company_name','id')->toArray();
              
              $party_added = $company_clients
                              ->where('created_by', $emp_id)
                              ->where('created_at', 'like', $date.'%')
                              ->pluck('id')
                              ->toArray();
  
              $unscheduled_effective_calls_clients = $orders
                                                      ->where('order_date', '=', $date)
                                                      ->whereNotIn('client_id', $client_ids)
                                                      ->pluck('client_id')
                                                      ->toArray();

              $emp[$date]['unscheduled_effective_calls'] = $company_clients
                                                  ->whereIn('id',$unscheduled_effective_calls_clients)
                                                  ->pluck('company_name','id')
                                                  ->toArray();
              
              $unscheduled_non_effective_calls_clients = $no_orders
                                                      ->where('date', '=', $date)
                                                      ->whereNotIn('client_id', $client_ids)
                                                      ->pluck('client_id')
                                                      ->toArray();

              $emp[$date]['unscheduled_non_effective_calls'] = $company_clients
                                                      ->whereIn('id',$unscheduled_non_effective_calls_clients)
                                                      ->pluck('company_name','id')
                                                      ->toArray();

              $employee_actual_visits_clients = array_unique(array_merge($orders_clients, $no_orders_clients));
              $unscheduled = array_unique(array_merge($unscheduled_effective_calls_clients, $unscheduled_non_effective_calls_clients));
              $actual_visits = array_unique(array_merge($unscheduled, $employee_actual_visits_clients));

              $emp[$date]['employee_actual_visits'] = $company_clients
                                                      ->whereIn('id',$actual_visits)
                                                      ->pluck('company_name','id')
                                                      ->toArray();
              $not_covered_array = array_diff($client_ids,$employee_actual_visits_clients);

              $emp[$date]['not_covered'] = $company_clients
                                          ->whereIn('id',$not_covered_array)
                                          ->pluck('company_name','id')
                                          ->toArray();
              $beat_names_lists = Beat::whereIn('id',$beat_ids)->orderby('name','asc')->get();
              $beat_names = array();
              foreach($beat_names_lists as $beat_names_list){
                  array_push($beat_names, $beat_names_list->name);
              }
              $emp[$date]['beats'] = $beat_names;
              $beatsVisited = array();
              $clientsVisited = array();
              $beatClients = json_decode($beat->beat_clients);
              if(!empty($beatClients)){
                foreach($beatClients as $key=>$beatClient){
                  if(!empty($beatClient)){
                    foreach($beatClient as $btClient){
                      if(!empty($btClient)){
                        if(in_array($btClient, $actual_visits)){
                            if(!(in_array($key, $beatsVisited))){
                                array_push($beatsVisited, $key);
                            }
                            array_push($clientsVisited, $btClient);
                        }
                      }
                    }
                  }
                }
              }
              $diffInarray = array_diff($actual_visits,$clientsVisited);
              foreach($actual_visits as $actual_visit){
                  if(!(in_array((string)$actual_visit, $clientsVisited))){
                      $getBeatID = DB::table('beat_client')->where('client_id', $actual_visit)->first();
                      if($getBeatID){
                          $gtbtID = $getBeatID->beat_id;
                          array_push($beatsVisited, $gtbtID);
                      }
                  }
              }
              
              $actual_beats = Beat::whereIn('id',$beatsVisited)->pluck('name')->toArray();
              $emp[$date]['beat_names'] = $actual_beats;
              $clients_ids[$date] = array(implode(',',array_unique(array_merge($orders_clients,$unscheduled_effective_calls_clients))), implode(',',array_unique(array_merge($no_orders_clients, $unscheduled_non_effective_calls_clients))), implode(',',$not_covered_array));

              unset($orders_clients);
              unset($no_orders_clients);
              unset($unscheduled_effective_calls_clients);
              unset($party_added);
              unset($unscheduled_non_effective_calls_clients);
              unset($employee_actual_visits_clients);
              unset($not_covered_array);
              unset($beat_ids);
          }
      }

      if(isset($emp)){
          return View::make('company.newreports.beatroute', compact('emp','emp_id','emp_name','clients_ids'))->render();
      }else{
          return View::make('company.newreports.beatroute')->render();
      }
  }

  /***
  * Stock Report
  ***/
  public function stockreport(){
    if(config('settings.stock_report')==0 || !(Auth::user()->can('stock-report-view'))){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');
    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

    $parties = Auth::user()->handleQuery('client')->select('id','company_name','client_type')->orderBy('company_name','asc')->get()->toJson();
    $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')
                    ->toArray();

    // Party-wise Latest Stock Report
    $single_reports_generated = GenerateReport::where('company_id', $company_id)
                                  ->where('report_type', '=', 'Stock Report')
                                  ->where('report_cat', 'Single Party')
                                  ->whereIn('generated_by', $handledEmployees)
                                  ->orderby('created_at', 'desc')
                                  ->get();
    // Single Party Historical Party Report
    // $multiple_reports_generated = GenerateReport::where('company_id', $company_id)
    //                                 ->where('report_type', '=', 'Stock Report')
    //                                 ->where('report_cat', '<>','Single Party')
    //                                 ->whereIn('generated_by', $handledEmployees)
    //                                 ->orderby('created_at', 'desc')
    //                                 ->get();
    return view('company.stockreports.stockreport', compact('parties', 'partytypes', 'single_reports_generated'));
  }

  public function partylateststockreports(Request $request){
    $company_id = config('settings.company_id');
    $client_id = $request->party_id;
    $handled_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $first_stock = Stock::where('stocks.company_id', $company_id)
                    ->whereIn('stocks.employee_id', $handled_employees)
                    ->where('stocks.client_id', $client_id)
                    ->orderby('stocks.stock_date_unix', 'DESC')
                    ->first();
    if($first_stock){
      $client_stocks = Stock::join('stock_details','stock_details.stock_id','stocks.id')
                          ->whereIn('stocks.employee_id', $handled_employees)
                          ->where('stocks.id', $first_stock->id)
                          ->get();
      $salesman_name =  getEmployeeName($first_stock->employee_id);
      $last_stock_date = $first_stock->stock_date;
    }else{
      $salesman_name =  NULL;
      $last_stock_date = NULL;
    }
    $products = Product::where('company_id', $company_id)
                ->orderby('product_name', 'ASC')
                ->get();
    $data = array();
    $count = 0;
    foreach($products as $product){
      $product_id = $product->id;
      $variant_id = NULL;
      $brand = getBrandName($product->brand);
      $brand_name = isset($brand)?$brand:NULL;
      $category = getCategory($product->category_id);
      $category_name = isset($category)?$category->name:NULL;
      $qty = 0;
      if($product->variant_flag==1){
        $product_variants = $this->getVariants($product_id);
        foreach($product_variants as $product_variant){
          $qty = 0;
          $variant_id = $product_variant->id;
          $count += 1;
          if($first_stock){
            foreach($client_stocks as $client_stock){
              if($client_stock->product_id==$product_id && $client_stock->variant_id==$variant_id){
                $qty = $client_stock->quantity;
              }
            }                        
          }
          $variant =  $product_variant->variant;
          $unit = getUnitName($product_variant->unit);
          $unit_name = isset($unit)?$unit:NULL;
          $data[$product_id][$variant_id]["count"] = $count;
          $data[$product_id][$variant_id]["product"] = $product->product_name;
          $data[$product_id][$variant_id]["variant"] = $variant;
          $data[$product_id][$variant_id]["unit"] = $unit_name; 
          $data[$product_id][$variant_id]["brand"] = $brand_name; 
          $data[$product_id][$variant_id]["category"] = $category_name; 
          $data[$product_id][$variant_id]["quantity"] = $qty;
        }
      }else{
        $count += 1;
        if($first_stock){
          foreach($client_stocks as $client_stock){
            if($client_stock->product_id==$product_id){
              $qty = $client_stock->quantity;
            }
          }
        }
        $unit = getUnitName($product->unit);
        $unit_name = isset($unit)?$unit:NULL;
        $data[$product_id][0]["count"] = $count;
        $data[$product_id][0]["product"] = $product->product_name;
        $data[$product_id][0]["variant"] = NULL;
        $data[$product_id][0]["unit"] = $unit_name;
        $data[$product_id][0]["brand"] = $brand_name; 
        $data[$product_id][0]["category"] = $category_name;
        $data[$product_id][0]["quantity"] = $qty;
      }
    }
    $info['data'] = $data;
    if(config('settings.ncal')==0)
      $info['date'] = $last_stock_date;
    else
      $info['date'] = isset($last_stock_date)?getDeltaDateForReport($last_stock_date):null;
    $info['salesman_name'] = $salesman_name;
    return $info;
  }

  public function singlepartystockreports(Request $request){
    $company_id = config('settings.company_id');
    $client_id = $request->party_id;
    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $client = Client::select("clients.id as client_id", "clients.company_name as name")
                ->where('company_id', $company_id)
                ->where('id', $client_id)
                ->first();
    
    $products = Product::where('products.company_id', $company_id)
                ->leftJoin('brands', 'brands.id', 'products.brand')
                ->leftJoin('categories', 'categories.id', 'products.category_id')
                ->leftJoin('unit_types', 'unit_types.id', 'products.unit')
                ->orderby('product_name', 'asc')
                ->get(['products.*', 'brands.name as brand_name', 'categories.name as category_name', 'unit_types.symbol as unit_name']);

    $header = array("Product Name", "Variant", "Unit", "Brand", "Category");
    $data = array();
    $stock_dates = $this->getStockDates($client_id, $handledEmployees);
    if(!empty($stock_dates)){
      foreach($stock_dates as $stock_date){
        $salesman_name = $stock_date['employee_name'];
        //getEmployeeName($stock_date['employee_id']);
        if(config('settings.ncal')==0){
          $date_salesman = isset($salesman_name)?$stock_date['stock_date']." (".$salesman_name.")":$stock_date['stock_date'];
        }else{
          $date_salesman = isset($salesman_name)?getDeltaDateForReport($stock_date['stock_date'])." (".$salesman_name.")":getDeltaDateForReport($stock_date['stock_date']);
        }
        array_push($header, $date_salesman);
      }
      $excel_data[] = $header;
        foreach($products as $product){
          $product_id = $product->id;
          $variant_id = NULL;
          //$brand = getBrandName($product->brand);
          $brand_name = $product->brand_name;
          //isset($brand)?$brand:NULL;
          // $category = getCategory($product->category_id);
          $category_name = $product->category_name;
          // isset($category)?$category->name:NULL;
          $qty = 0;
          if($product->variant_flag==1){
            $product_variants = $this->getVariants($product_id);
            foreach($product_variants as $product_variant){
              $variant_id = $product_variant->id;
              $variant =  $product_variant->variant;
              $unit = getUnitName($product_variant->unit);
              $unit_name = $product->unit_name;
              isset($unit)?$unit:NULL;
              $data[$product_id][$variant_id]["product"] = $product->product_name;
              $data[$product_id][$variant_id]["variant"] = $variant;
              $data[$product_id][$variant_id]["unit"] = $unit_name; 
              $data[$product_id][$variant_id]["brand"] = $brand_name; 
              $data[$product_id][$variant_id]["category"] = $category_name; 
              $counter = 0;
              foreach($stock_dates as $stock_date){
                ++$counter;
                $stock_qty_date = $this->clientProductLastStockDate($client_id, $product_id, $variant_id, $stock_date, $handledEmployees);
                if(!(empty($stock_qty_date))){ 
                  $qty = $stock_qty_date->quantity;
                }
                else{
                  $qty = 0;
                }
                $data[$product_id][$variant_id][$stock_date['stock_date_unix'].$stock_date['employee_id']] = "$qty";
                $employee_name = NULL;
              }
            }
          }else{
            // $unit = getUnitName($product->unit);
            $unit_name = $product->unit_name;
            // isset($unit)?$unit:NULL;
            $data[$product_id][0]["product"] = $product->product_name;
            $data[$product_id][0]["variant"] = NULL;
            $data[$product_id][0]["unit"] = $unit_name;
            $data[$product_id][0]["brand"] = $brand_name; 
            $data[$product_id][0]["category"] = $category_name;
            $counter = 0;
            foreach($stock_dates as $stock_date){
              ++$counter;
              $stock_qty_date = $this->clientProductLastStockDate($client_id, $product_id, $variant_id, $stock_date, $handledEmployees);
              if(!empty(isset($stock_qty_date))){
                  $qty = $stock_qty_date->quantity;
              }
              else{
                  $qty = 0;
              }
              $data[$product_id][0][$stock_date['stock_date_unix'].$stock_date['employee_id']] = "$qty";
              $employee_name = NULL;
            }
          }
        }
        foreach($data as $d=>$arr){
          foreach($arr as $a){
            $excel_data[] = $a;
          }
        }
        $type = 'xlsx';
        $filename = str_replace(" ","",$client->name).'_'."StockReport(Hist.)_".'_'.time();
        $excel_export[$client->name] = $excel_data;
        Excel::store(new MultiSheetExport($excel_export), urlencode($filename).'.'.$type, 'stockReports');

        $filename_download = urlencode(str_replace(" ","",$client->name).'_'."StockReport(Hist.)");
        $report = new GenerateReport;
        $report->company_id = config('settings.company_id');
        $report->filename = $filename_download.'.xlsx';
        $report->report_type = 'Stock Report';
        $report->report_cat = 'Single Party';//'Party_Consolidated';
        $report->party_id = $client_id;
        $report->employee_id = NULL;
        $report->date_range = date('Y-m-d');
        $report->generated_by = Auth::user()->EmployeeId();
        $report->download_link = 'http://'.$_SERVER['HTTP_HOST'].'/cms/storage/excel/reports/stockReports/'.urlencode($filename).'.xlsx';
        $report->save();
        $data = [];
        $data['msg'] = "Generated";
        $data['row'] = $report; 
    }else{
      $data['msg'] = "No Records.";
      $data['link'] = NULL;
    }
    return $data;
  }

  public function partieslateststockreports(Request $request){
    ini_set('max_execution_time', 600);
    try{
      $company_id = config('settings.company_id');
      $client_id = json_decode($request->party_id);
      // $client_id = $request->party_id;
      $startDate = $request->startDate; 
      $endDate = $request->endDate;
      if($startDate==$endDate){
        if(config('settings.ncal')==0){
          $date = $startDate;
        }else{
          $date = getDeltaDateForReport($startDate);
        }
      }else{
        if(config('settings.ncal')==0){
          $date = $startDate.'-'.$endDate;
        }else{
          $date = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
        }
      }
  
      $handled_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
  
      $prepQuery = Stock::where('stocks.company_id', $company_id)
                        ->whereIn('stocks.client_id', $client_id)
                        ->whereIn('stocks.employee_id', $handled_employees)
                        ->whereBetween('stocks.stock_date', [$startDate, $endDate]);
  
      $stockCount = (clone $prepQuery);
      $stock_exists = $stockCount->count();
      $stock_client_ids = $stockCount->distinct('client_id')->pluck('client_id')->toArray();
      
      if($stock_exists>0){
  
        // $client = Client::select("clients.id as client_id", "clients.company_name as name", "clients.client_type")->where('company_id', $company_id)
        //             ->whereIn('clients.id', $stock_client_ids)
        //             ->orderby('clients.company_name', 'asc')
        //             ->get();
        // $party_types = PartyType::select('partytypes.id', 'partytypes.name')
        //                 ->where('company_id', $company_id)
        //                 ->get();
  
        // $client->map(function ($item) use ($party_types) {
        //   if(isset($item->client_type)){
        //     try{
        //       $item['client_type_name'] = $party_types->filter(function ($party_item, $party_key) use ($item) {
        //       return $party_item->id==$item->client_type;
        //       })->first()->name;
        //     }catch(\Exception $e){
        //       $item['client_type_name'] = "";
        //     }
        //   }else{
        //     $item['client_type_name'] = "Unspecified";
        //   }
        // });
  
        // $getStocks = (clone $prepQuery);
        // $stocks_taken = Stock::where('stocks.company_id', $company_id)->join('stock_details', 'stock_details.stock_id', 'stocks.id')
        //                 ->whereIn('stocks.client_id', $stock_client_ids)
        //                 ->whereIn('stocks.employee_id', $handled_employees)
        //                 ->whereBetween('stocks.stock_date', [$startDate, $endDate])
        //                 ->get();
  
        // $products = Product::where('company_id', $company_id)
        //             ->orderby('category_id', 'DESC')
        //             ->get();
  
        // $brands = Brand::select('brands.id', 'brands.name')
        //             ->where('company_id', $company_id)
        //             ->orderby('name', 'ASC')
        //             ->get()->toArray();
        // $unspecified_brand["id"] = 0;
        // $unspecified_brand["name"] = "Unspecified";
        // array_push($brands, $unspecified_brand);
  
        // $categories = Category::select('categories.id', 'categories.name')
        //                 ->where('company_id', $company_id)
        //                 ->orderby('name', 'ASC')
        //                 ->get()->toArray();
        // $unspecified_category["id"] = 0;
        // $unspecified_category["name"] = "Unspecified";
        // array_push($categories, $unspecified_category);
        $report_type = $request->reptype;
        // if(count($client_id)==1){
        //   $name = str_replace(" ","",getClient($client_id)->company_name);
        // }else{
        //   $name = "Multiple";
        // }
        // $time = time();
        // $type = 'xlsx';
        if($report_type == "brand"){
          $cat = "By Brand";
        //   $filename_download = $name.'Stock_Reports_By_Brand_'.$date;
        //   $data_sheet = $this->getBrandExcelReport($brands, $products, $client, $stocks_taken, $repKey="stocks");
  
        //   $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'stockReports');
  
        }else if($report_type == "category"){
          $cat = "By Category";
        //   $filename_download = $name.'Stock_Reports_By_Category_'.$date;
        //   $data_sheet = $this->getCategoryExcelReport($categories, $products, $client, $stocks_taken, $repKey="stocks");
  
        //   $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'stockReports');
  
        }else if($report_type == "consolidated"){
          $cat = "Consolidated";
        //   $filename_download = $name.'Stock_Reports_Cosnolidated_'.$date;
        //   $data_sheet = $this->getConsolidatedExcelReport($products, $client, $stocks_taken, $repKey="stocks");
  
        //   $exportSheet['Consolidated'] = $data_sheet;
        //   $_excel = Excel::store(new MultiSheetExport($exportSheet), urlencode($filename_download.$time).'.'.$type, 'stockReports');
  
        } 
        $msg['msg'] = "Generated";
        $report = new GenerateReport;
        $report->company_id = config('settings.company_id');
        $report->filename = NULL;//urlencode($filename_download.'.xlsx');
        $report->report_type = 'Stock Report';
        $report->report_cat = $cat;
        $report->party_id = json_encode($client_id);
        $report->employee_id = NULL;
        $report->start_date = $startDate;
        $report->end_date = $endDate;
        $report->generated_by = Auth::user()->EmployeeId();
        $report->download_link = NULL;//'http://'.$_SERVER['HTTP_HOST'].'/cms/storage/excel/reports/stockReports/'.urlencode($filename_download.$time).'.xlsx';
        $report->save();
        // Session::flash("success", "Your Report will be generated soon.");

        // return back();
        // $msg['link'] = $report;        
        // $msg['status'] = true;
        // return response()->json([
        //   'status' => true,
        //   'msg' => "Your Report will be generated soon."
        // ]);
        Session::flash("success","Your Report will be generated soon.");

        return back();
      }else{
        // $msg['msg'] = "No Records";
        // $msg['link'] = NULL;
        // $msg['status'] = false;
        // Session::flash("warning", "There are no records between selected dates.");

        // return back();
        
        // return response()->json([
        //   'status' => false,
        //   'msg' => "There are no records between selected dates."
        // ]);

        Session::flash("success", "There are no records between selected dates.");

        return back();
        
      }
    }catch(\Exception $e){
      Log::error($e->getLine());
      // $msg['msg'] = $e->getMessage();
      // $msg['link'] = NULL;
      // $msg['status'] =false;
      Session::flash("warning", $e->getMessage());

      return back();
    }
    return $msg;
  }

  public function stockreportsdtDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'report_type',
      2 => 'date_range',
      3 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query =GenerateReport::where('company_id', $company_id)
              ->where('report_type', '=', 'Stock Report')
              ->where('report_cat', '<>','Single Party')
              ->whereIn('generated_by', $handledEmployees)
              ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){

        if(count(json_decode($reports->party_id))>1){
          $name_content = "Multiple Party -- "; 
        }else{
          $gtClient = getClient(json_decode($reports->party_id));
          $name_content = isset($gtClient)? $gtClient->company_name . " -- ":NULL;
        } 
        $decode_party = json_decode($reports->party_id);
        if(count($decode_party)==1) $client_names = Client::whereIn('id', $decode_party)->pluck('company_name')->toArray(); 
        else $client_names = Client::whereIn('id', $decode_party)->pluck('company_name')->toArray();
        
        $name_contents = implode(',', $client_names);
        $contents = "<b>The report was generated for following parties:-</b><br/><b>Parties:-</b>"."$name_contents";

        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'. $contents .'"></span>';
        $name_content .= $reports->report_type . " -- " . $reports->report_cat . $spanContent; 

        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $tooltip_content = "$reports->tooltip_content";
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';

        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['report_type'] = $name_content;
        $nestedData['date_range'] = $date_range;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }

    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }

  public function getBrandExcelReport($brands, $products, $client, $stocks_taken, $report_key){
    foreach($brands as $brand){
      if($brand['id']!=0){
        $this_brand_product = $products->where('brand', $brand['id'])->sortByDesc('category_id');
      }else{
        $this_brand_product = $products->where('brand', NULL)->sortByDesc('category_id');
      }
      $_excel;
      if($this_brand_product->count()>0){
        $brand_header = array($brand['name']);
        $brand_array = $this->getHeaderArray($this_brand_product, $search_by="brand");
        if($report_key=="stocks"){
          $sortColumn = "stock_date_unix";
          $first_header = array_merge(array("", "", "", "", ""), $brand_array["brandCatArr"]);
          $second_header = array_merge(array("Party Name", "Party Type", "Last Stock Date", "Last Stock Taken By", "Total Stock Quantity"), $brand_array["products"]);
          $third_header = array_merge(array("", "", "", "", ""), $brand_array["product_variants"]);
          $total_by_col = array("Total:-", "", "", "");
        }else{
          $sortColumn = "return_unixtime";
          $first_header = array_merge(array("", "", ""), $brand_array["brandCatArr"]);
          $second_header = array_merge(array("Party Name", "Party Type", "Total Returned Product"), $brand_array["products"]);
          $third_header = array_merge(array("", "", ""), $brand_array["product_variants"]);
          $total_by_col = array("Total:-", "");
        }
        $prod_total = array();
        $data[] = $brand_header;
        $data[] = $first_header;
        $data[] = $second_header;
        $data[] = $third_header;
        $total_stock_col = 0;
        foreach($client as $client_item){
          $total_stock = 0;
          $client_stock = $stocks_taken->where('client_id', $client_item->client_id)->sortByDesc($sortColumn);
          if($report_key=="stocks"){
            if(config('settings.ncal')==0){
              $latest_stock_date = ($client_stock->first())?$client_stock->first()->stock_date:NULL;
            }else{
              $latest_stock_date = ($client_stock->first())?getDeltaDateForReport($client_stock->first()->stock_date):NULL;
            }
            $latest_stock_taken_by = ($client_stock->first())?$client_stock->first()->employee_id:NULL;
            $emp_name = isset($latest_stock_taken_by)?getEmployeeName($latest_stock_taken_by):NULL;
            $client_details = array($client_item->name, $client_item->client_type_name, $latest_stock_date, $emp_name, "");
          } else{
            $client_details = array($client_item->name, $client_item->client_type_name, "");
          }
          foreach($brand_array['product_variant_ids'] as $prod_id=>$variant_id){
            if($variant_id == 0){
              if ($report_key=="stocks") {
                $stocks_exists = $client_stock->where('product_id', $prod_id)->first();
                if ($stocks_exists) {
                  $stock_taken = $stocks_exists->quantity;
                } else {
                  $stock_taken = 0;
                }
              }else{
                $stocks_exists = $client_stock->where('product_id', $prod_id);
                if ($stocks_exists->first()) {
                  $stock_taken = $stocks_exists->sum('quantity');
                } else {
                  $stock_taken = 0;
                }
              }
              $total_stock += $stock_taken;
              array_push($client_details, "$stock_taken");
              $prod_total[$prod_id][] = $stock_taken;
            }else{
              foreach($variant_id as $var_id){
                if ($report_key=="stocks") {
                  $stocks_exists = $client_stock->where('product_id', $prod_id)
                            ->where('variant_id', $var_id)->first();
                  if ($stocks_exists) {
                    $stock_taken = $stocks_exists->quantity;
                  } else {
                    $stock_taken = 0;
                  }

                } else {
                  $stocks_exists = $client_stock->where('product_id', $prod_id)
                                  ->where('variant_id', $var_id);
                  if ($stocks_exists->first()) {
                    $stock_taken = $stocks_exists->sum('quantity');
                  } else {
                    $stock_taken = 0;
                  }
                }

                $total_stock += $stock_taken;
                array_push($client_details, "$stock_taken");
                $prod_total[$prod_id][$var_id][] = $stock_taken;
              }
            }
          }
          if ($report_key=="stocks") {
            $client_details[4] = "$total_stock";
          }else{
            $client_details[2] = "$total_stock";

          }
          $data[] = $client_details;
          $total_stock_col += $total_stock;
        }
        array_push($total_by_col, "$total_stock_col");
        $getTotal = $this->getColTotal($total_by_col, $brand_array['product_variant_ids'], $prod_total);
        $data[] = $getTotal;
        $data_sheet[$brand["name"]] = $data;
        unset($data);
        unset($total_by_col);
      }
    }

    return $data_sheet;
  }

  public function getCategoryExcelReport($categories, $products, $client, $stocks_taken, $report_key){
    ini_set('memory_limit', '2048M');
    foreach($categories as $category){
      if($category['id']!=0){
        $this_category_product = $products->where('category_id', $category['id'])->sortByDesc('brand');
      }else{
        $this_category_product = $products->where('category_id', NULL)->sortByDesc('brand');
      }
      $_excel;
      if($this_category_product->count()>0){
        $cat_header = array($category['name']);
        $cat_array = $this->getHeaderArray($this_category_product, $search_by="category");
        if($report_key=="stocks"){
          $sortColumn = "stock_date_unix";
          $first_header = array_merge(array("", "", "", "", ""), $cat_array["brandCatArr"]);
          $second_header = array_merge(array("Party Name", "Party Type", "Last Stock Date", "Last Stock Taken By", "Total Stock Quantity"), $cat_array["products"]);
          $third_header = array_merge(array("", "", "", "", ""), $cat_array["product_variants"]);
          $total_by_col = array("Total:-", "", "", "");
        }else{
          $sortColumn = "return_unixtime";
          $first_header = array_merge(array("", "", ""), $cat_array["brandCatArr"]);
          $second_header = array_merge(array("Party Name", "Party Type", "Total Returned Products"), $cat_array["products"]);
          $third_header = array_merge(array("", "", ""), $cat_array["product_variants"]);
          $total_by_col = array("Total:-", "");

        }
        $prod_total = array();
        $data[] = $cat_header;
        $data[] = $first_header;
        $data[] = $second_header;
        $data[] = $third_header;
        $total_stock_col = 0;
        foreach($client as $client_item){
          $total_stock = 0;
          $client_stock = $stocks_taken->where('client_id', $client_item->client_id)->sortByDesc($sortColumn);
          if($report_key=="stocks"){
            $latest_stock_date = ($client_stock->first())?$client_stock->first()->stock_date:NULL;
            $latest_stock_taken_by = ($client_stock->first())?$client_stock->first()->employee_id:NULL;
            $emp_name = isset($latest_stock_taken_by)?getEmployeeName($latest_stock_taken_by):NULL;
            $client_details = array($client_item->name, $client_item->client_type_name, $latest_stock_date, $emp_name, "");
          }else{
            $client_details = array($client_item->name, $client_item->client_type_name, "");
          }
          foreach($cat_array['product_variant_ids'] as $prod_id=>$variant_id){
            if($variant_id == 0){
              if($report_key=="stocks"){
                $stocks_exists = $client_stock->where('product_id', $prod_id)->first();
                if($stocks_exists){
                  $stock_taken = "$stocks_exists->quantity";
                }else{
                  $stock_taken = 0;
                }
              }else{
                $stocks_exists = $client_stock->where('product_id', $prod_id);
                if($stocks_exists->first()){
                  $stock_taken = $stocks_exists->sum('quantity');
                }else{
                  $stock_taken = 0;
                }
              }
              $total_stock += $stock_taken;
              array_push($client_details, "$stock_taken");
              $prod_total[$prod_id][] = $stock_taken;
            }else{
              if($report_key=="stocks"){
                foreach($variant_id as $var_id){
                  $stocks_exists = $client_stock->where('product_id', $prod_id)
                              ->where('variant_id', $var_id)->first();
                  if($stocks_exists){
                    $stock_taken = $stocks_exists->quantity;
                  }else{
                    $stock_taken = 0;
                  }
                  $total_stock += $stock_taken;
                  array_push($client_details, "$stock_taken");
                  $prod_total[$prod_id][$var_id][] = $stock_taken;
                }
              }else{
                foreach($variant_id as $var_id){
                  $stocks_exists = $client_stock->where('product_id', $prod_id)
                              ->where('variant_id', $var_id);
                  if($stocks_exists->first()){
                    $stock_taken = $stocks_exists->sum('quantity');
                  }else{
                    $stock_taken = 0;
                  }
                  $total_stock += $stock_taken;
                  array_push($client_details, "$stock_taken");
                  $prod_total[$prod_id][$var_id][] = $stock_taken;
                }
              }
            }
          }
          if($report_key=="stocks"){
            $client_details[4] = "$total_stock";
          }else{
            $client_details[2] = "$total_stock";
          }
          $data[] = $client_details;
          $total_stock_col += $total_stock;
        }
        array_push($total_by_col, "$total_stock_col");
        $getTotal = $this->getColTotal($total_by_col, $cat_array['product_variant_ids'], $prod_total);
        $data[] = $getTotal;
        $data_sheet[$category["name"]] = $data;
        unset($data);
        unset($total_by_col);
      }
    }

    return $data_sheet;
  }

  public function getConsolidatedExcelReport($products, $client, $stocks_taken, $report_key){
    ini_set('memory_limit', '2048M');
    $_excel;
    $con_header = array("Consolidated");
    $con_array = $this->getHeaderArray($products, $search_by="consolidated");
    if($report_key=="stocks"){
      $sortColumn = "stock_date_unix";
      $first_header = array_merge(array("", "", "", "", ""), $con_array["brandCatArr"]);
      $second_header = array_merge(array("Party Name", "Party Type", "Last Stock Date", "Last Stock Taken By", "Total Stock Quantity"), $con_array["products"]);
      $third_header = array_merge(array("", "", "", "", ""), $con_array["product_variants"]);
      $total_by_col = array("Total:-", "", "", "");
    }else{
      $sortColumn = "return_unixtime";
      $first_header = array_merge(array("", "", ""), $con_array["brandCatArr"]);
      $second_header = array_merge(array("Party Name", "Party Type", "Total Stock Quantity"), $con_array["products"]);
      $third_header = array_merge(array("", "", ""), $con_array["product_variants"]);
      $total_by_col = array("Total:-", "");

    }
    $prod_total = array();
    $data[] = $con_header;
    $data[] = $first_header;
    $data[] = $second_header;
    $data[] = $third_header;
    $total_stock_col = 0;
    foreach($client as $client_item){
      $total_stock = 0;
      $client_stock = $stocks_taken->where('client_id', $client_item->client_id)->sortByDesc($sortColumn);
      if($report_key=="stocks"){
        $latest_stock_date = ($client_stock->first())?$client_stock->first()->stock_date:NULL;
        $latest_stock_taken_by = ($client_stock->first())?$client_stock->first()->employee_id:NULL;
        $emp_name = isset($latest_stock_taken_by)?getEmployeeName($latest_stock_taken_by):NULL;
        $client_details = array($client_item->name, $client_item->client_type_name, $latest_stock_date, $emp_name, "");
      }else{
        $client_details = array($client_item->name, $client_item->client_type_name, "");

      }
      foreach($con_array['product_variant_ids'] as $prod_id=>$variant_id){
        if($variant_id == 0){
          if($report_key=="stocks"){
            $stocks_exists = $client_stock->where('product_id', $prod_id)->first();
            if($stocks_exists){
              $stock_taken = $stocks_exists->quantity;
            }else{
              $stock_taken = 0;
            }
          }else{
            $stocks_exists = $client_stock->where('product_id', $prod_id);
            if ($stocks_exists->first()) {
                $stock_taken = $stocks_exists->sum('quantity');
            } else {
                $stock_taken = 0;
            }

          }
          $total_stock += $stock_taken;
          array_push($client_details, "$stock_taken");
          $prod_total[$prod_id][] = $stock_taken;
        }else{
          if($report_key=="stocks"){
            foreach($variant_id as $var_id){
              $stocks_exists = $client_stock->where('product_id', $prod_id)
                                  ->where('variant_id', $var_id)->first();
              if($stocks_exists){
                $stock_taken = $stocks_exists->quantity;
              }else{
                $stock_taken = 0;
              }
              $total_stock += $stock_taken;
              array_push($client_details, "$stock_taken");
              $prod_total[$prod_id][$var_id][] = $stock_taken;
            }
          }else{
            foreach($variant_id as $var_id){
              $stocks_exists = $client_stock->where('product_id', $prod_id)
                                  ->where('variant_id', $var_id);
              if($stocks_exists->first()){
                $stock_taken = $stocks_exists->sum('quantity');
              }else{
                $stock_taken = 0;
              }
              $total_stock += $stock_taken;
              array_push($client_details, "$stock_taken");
              $prod_total[$prod_id][$var_id][] = $stock_taken;
            }
          }
        }
      }
      if($report_key=="stocks"){
        $client_details[4] = "$total_stock";
      }else{
        $client_details[2] = "$total_stock";

      }
      $data[] = $client_details;
      $total_stock_col += $total_stock;
    }
    array_push($total_by_col, "$total_stock_col");
    $getTotal = $this->getColTotal($total_by_col, $con_array['product_variant_ids'], $prod_total);
    $data[] = $getTotal;
    $data_sheet = $data;
    unset($data);
    unset($total_by_col);

    return $data_sheet;
  }

  public function loadParties(Request $request){
    $company_id = config('settings.company_id');
    if ($request->ajax()){
      $page = $request->get('page');
      $resultCount = 50;
      $offset = ($page - 1) * $resultCount;
      if(empty($request->get('term'))){
        $parties = Auth::user()->handleQuery('client')->orderBy('company_name','asc')->skip($offset)->take($resultCount)->get(['id',DB::raw('company_name as text')]);
      }else{
        $parties = Auth::user()->handleQuery('client')->where('company_name', 'LIKE', '%' . $request->get("term").'%')->orderBy('company_name','asc')->skip($offset)->take($resultCount)->get(['id',DB::raw('company_name as text')]);
      }
      
      $count =  Auth::user()->handleQuery('client')->count();
      $endCount = $offset + $resultCount;
      $morePages = $endCount < $count;

      $results = array(
          "results" => $parties,
          "pagination" => array(
          "more" => $morePages
      )
      );

      return response()->json($results);
    }
  }

  public function fetchpartylist(Request $request){
    $company_id = config('settings.company_id');
    $partytypes = $request->sel_party_types;
    $parties = Auth::user()->handleQuery('client')->whereIn('clients.client_type', $partytypes)->select('id','company_name','client_type')->orderBy('company_name','asc')->get()->toJson();

    return $parties;
  }

  public function salesmanandpartylist(Request $request){
    $company_id = config('settings.company_id');
    $search_type = $request->search_type;
    if ($search_type == 'custom_party') {
      $party_types = PartyType::where('company_id', $company_id)->pluck('id')
                      ->toArray();
      array_push($party_types, "0");
      if(!empty($party_types)){
        $records = Auth::user()->handleQuery('client')
                  ->whereIn('client_type', $party_types)
                  ->orderby('company_name', 'asc')
                  ->pluck('company_name', 'id')
                  ->toArray();
      }else{
        $records = Auth::user()->handleQuery('client')
                  ->orderby('company_name', 'asc')
                  ->pluck('company_name', 'id')
                  ->toArray();

      }
    } elseif ($search_type == 'custom_salesman') {
      $records = Auth::user()->handleQuery('employee')
                ->orderby('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();
    }elseif ($search_type == 'beat_wise') {
      $emp_client_handles = DB::table('handles')->where('employee_id', Auth::user()->EmployeeId())->pluck('client_id')->toArray();
      $empBeats = DB::table('beat_client')->whereIn('client_id', $emp_client_handles)->pluck('beat_id', 'client_id')->toArray();
      $records = Beat::where('company_id', $company_id)->whereIn('id', $empBeats)->whereStatus('Active')->orderby('name', 'desc')
                ->pluck('name', 'id')
                ->toArray();
      $getClientsWithBeats = array_keys($empBeats);
      $checkUnspecifiedExists = array_diff($emp_client_handles, $getClientsWithBeats);
      if(count($checkUnspecifiedExists)>0) $records[0] = "Unspecified";
    }elseif ($search_type == 'order_status_wise') {
      $records = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                ->pluck('title', 'id')
                ->toArray();
    } else {
      $records = array();
    }
    return $records;
  }

  public function getStockDates($client_id, $handledEmployees){
    $order_dates = Stock::where('stocks.client_id', $client_id)
                    ->whereIn('stocks.employee_id', $handledEmployees)
                    ->leftJoin('employees', 'employees.id', 'stocks.employee_id')
                    ->orderby('stocks.stock_date_unix', 'DESC')
                    ->select('stocks.stock_date','stocks.employee_id', 'employees.name as employee_name', 'stocks.stock_date_unix')
                    ->get()->toArray();

    return $order_dates;
  }

  public function clientProductLastStockDate($client_id, $product_id, $variant_id, $stock_date,$handledEmployees){
    $company_id = config('settings.company_id');

    $prepQuery = Stock::join('stock_details','stock_details.stock_id','stocks.id')
                  ->select("stocks.stock_date as last_stock_date", "stocks.employee_id as employee", "stock_details.quantity")
                  ->where('stocks.company_id', $company_id)
                  ->whereIn('stocks.employee_id', $handledEmployees)
                  ->where('stocks.client_id', $client_id)
                  ->where('stock_details.product_id', $product_id);

    if($variant_id==NULL){
      $query2 = (clone $prepQuery);
      if($stock_date==NULL){
        $stock_det = $query2->whereNULL('stock_details.variant_id')
                    ->orderby('stocks.stock_date','DESC')
                    ->first();
      }else{
        $stock_det = $query2->whereNULL('stock_details.variant_id')
                    ->where('stocks.employee_id',$stock_date['employee_id'])
                    ->where('stocks.stock_date_unix',$stock_date['stock_date_unix'])
                    ->first();
      }
    }else{
      $query2 = (clone $prepQuery);
      if($stock_date==NULL){
        $stock_det = $query2->where('stock_details.variant_id', $variant_id)
                    ->orderby('stocks.stock_date','DESC')
                    ->first();
      }else{
        $stock_det = $query2->where('stock_details.variant_id', $variant_id)
                    ->where('stocks.employee_id',$stock_date['employee_id'])
                    ->where('stocks.stock_date_unix',$stock_date['stock_date_unix'])
                    ->first();
      }
    }

    return $stock_det;
  }

  public function getHeaderArray($brandOrCatProd, $search_by){
    $header = array();
    $prod_header = array();
    $prod_variant_header = array();
    $prod_variant_ids = array();

    foreach($brandOrCatProd as $product){
      $product_id = $product->id;
      $product_name = $product->product_name;
      if($search_by == "brand"){
        $get_category_name = isset($product->category_id)?getCategory($product->category_id):NULL;
        $category = isset($get_category_name)?$get_category_name->name:NULL;
        $name = isset($category)?$category:"Unspecified";
      }elseif($search_by == "category"){
        $brand = isset($product->brand)?getBrandName($product->brand):NULL;
        $name = isset($brand)?$brand:"Unspecified";
      }elseif($search_by == "consolidated"){
        $get_category_name = isset($product->category_id)?getCategory($product->category_id):null;
        $category = isset($get_category_name)?$get_category_name->name:null;
        // $category = isset($product->category_id)?getCategory($product->category_id)->name:NULL;
        $name = isset($category)?$category:"Unspecified";
        $brand = isset($product->brand)?getBrandName($product->brand):NULL;
        $brand_name = isset($brand)?$brand:NULL;
        $product_name = isset($brand_name)?$product_name." (".$brand_name.")":$product_name;
      }
      
      if($product->variant_flag==1){
        $prod_variant_ids[$product_id] = array();
        $get_product_variants = $this->getVariants($product_id);
        foreach($get_product_variants as $variants){
          array_push($prod_variant_ids[$product_id], $variants->id);
          $variant_name = $variants->variant;
          $unit_name = isset($variants->unit)?"(".getUnitName($variants->unit).")":null; 
          if(in_array($name, $header)){
            array_push($header, NULL);
          }else{
            array_push($header, $name);
          }
          if(in_array($product_name, $prod_header)){
            array_push($prod_header, NULL);
          }else{
            array_push($prod_header, $product_name);
          }
          array_push($prod_variant_header, $variant_name.$unit_name);
        }
      }else{
        $prod_variant_ids[$product_id] = 0;
        $unit_name = isset($product->unit)?"(".getUnitName($product->unit).")":null;
        if(in_array($name, $header)){
          array_push($header, NULL);
        }else{
          array_push($header, $name);
        }
        if(in_array($product_name, $prod_header)){
          array_push($prod_header, NULL);
        }else{
          array_push($prod_header, $product_name);
        }
        array_push($prod_variant_header, $unit_name);
      }
    }
    $data['brandCatArr'] = $header;
    $data['products'] = $prod_header;
    $data['product_variants'] = $prod_variant_header; 
    $data['product_variant_ids'] = $prod_variant_ids;

    return $data;
  }

  public function getColTotal($init_arr, $prods, $prod_total){
    foreach($prods as $prod_id=>$variant_id){
      if($variant_id == 0){
        $sum = array_sum($prod_total[$prod_id]);
        array_push($init_arr, "$sum");
      }else{
        foreach($variant_id as $var_id){
          $sum = array_sum($prod_total[$prod_id][$var_id]);
          array_push($init_arr, "$sum");
        }
      }
    }
    return $init_arr;
  }

  /***
   * Product Returns
  ***/
  public function returnsReport(){
    if(config('settings.returns')==0 || !(Auth::user()->can('return-report-view'))){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');
    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $parties = Auth::user()->handleQuery('client')->select('id','company_name','client_type')->orderBy('company_name','asc')->get()->toJson();
    $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')
                    ->toArray();

    // $reports_generated = GenerateReport::where('company_id', $company_id)
    //                                 ->where('report_type', '=', 'Return Report')
    //                                 ->whereIn('generated_by', $handledEmployees)
    //                                 ->orderby('id', 'desc')->whereNotNull('report_cat')
    //                                 ->get();

    // $productReturns = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
    //                     ->where('returns.company_id', $company_id)
    //                     ->whereDate('returns.return_date', '>=', Carbon::now()->subDays(30))
    //                     ->leftjoin('products', 'return_details.product_id', 'products.id')
    //                     ->leftjoin('product_variants', 'return_details.variant_id', 'product_variants.id');

    // $barPlotData = (clone $productReturns);
    // $barPlotData = $barPlotData->select(DB::raw("SUM(return_details.quantity) as sum"), "return_details.product_id", "return_details.variant_id", "products.product_name as prod_name", "product_variants.variant as var_name", "return_details.unit_name", DB::raw("(CASE WHEN ISNULL(product_variants.variant) THEN CONCAT(products.product_name, '') ELSE CONCAT(products.product_name, ', ', product_variants.variant) END) as product_details"))
    //                     ->groupBy('return_details.product_id', 'return_details.variant_id')
    //                     ->orderBy(DB::raw("SUM(return_details.quantity)"), "desc")
    //                     ->take(10)->get()->toArray();
    $barPlotData = array("sum" => array() ,"unit_name"  => array(), "product_id"  => array(), "variant_id" => array(), "product_details" => array());
    $sum = array_column($barPlotData, 'sum');
    $units = array_column($barPlotData, 'unit_name');
    $product_id = array_column($barPlotData, 'product_id');
    $variant_id = array_column($barPlotData, 'variant_id');

    $product_name = array_column($barPlotData, 'product_details');


    // $piePlotReasonsData = (clone $productReturns);
    // $piePlotReasonsData = $piePlotReasonsData->leftJoin('returnreasons', 'returnreasons.id', 'return_details.reason')->select('returnreasons.name as name', DB::raw("COUNT(return_details.reason) as y"),'returnreasons.name as drilldown', 'return_details.reason as reasonId')
    //                     ->groupBy('return_details.reason')
    //                     ->orderBy(DB::raw("COUNT(return_details.reason)"), "desc")
    //                     ->get()->toArray();

    $piePlotReasonsData = array();

    $drillDownReasonData = array();
    // foreach($piePlotReasonsData as $reasonGroupedData){
    //   $reasonDrillDownData = array();
    //   $getDetailsSum = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
    //                     ->where('returns.company_id', $company_id)
    //                     ->where('return_details.reason', $reasonGroupedData['reasonId'])
    //                     ->whereDate('returns.return_date', '>=', Carbon::now()->subDays(30))
    //                     ->leftjoin('products', 'return_details.product_id', 'products.id')
    //                     ->leftjoin('product_variants', 'return_details.variant_id', 'product_variants.id')
    //                     ->groupBy('return_details.product_id', 'return_details.variant_id', 'return_details.reason' )
    //                     ->get([ DB::raw("(CASE WHEN ISNULL(product_variants.variant) THEN CONCAT(products.product_name, ' (', SUM(return_details.quantity), ' ', return_details.unit_name,')') ELSE CONCAT(products.product_name, ', ', product_variants.variant,' (', SUM(return_details.quantity), ' ',return_details.unit_name,') ') END) as product_name"), DB::raw("COUNT(return_details.product_id) as sum") ])->toArray();

    //   $reasonDrillDownData['name'] = $reasonGroupedData['drilldown'];
    //   $reasonDrillDownData['id'] = $reasonGroupedData['drilldown'];
    //   $reasonDrillDownData['data'] = array();

    //   foreach($getDetailsSum as $getDetailSum){
    //     $reasonProductDetails = array();
    //     array_push($reasonProductDetails, $getDetailSum['product_name'], (int)$getDetailSum['sum']);

    //     array_push($reasonDrillDownData['data'], $reasonProductDetails);
    //   }
      
    //   array_push($drillDownReasonData, $reasonDrillDownData);
    // }

    return view('company.returnsreports.returnreport')
              ->with('parties', $parties)
              ->with('partytypes', $partytypes)
              // ->with('reports_generated', $reports_generated) 
              ->with('sum', json_encode($sum, JSON_NUMERIC_CHECK))
              ->with('units', $units)
              ->with('product_name', $product_name)
              ->with('productReturnsReasonCountQuery', $piePlotReasonsData)
              ->with('productReturnsDetailsCountQuery', $drillDownReasonData);
  }

  public function generateReturnsReport(Request $request){
    $company_id = config('settings.company_id');
    $client_id = $request->party_id;
    $startDate = $request->startDate; 
    $endDate = $request->endDate;
    $report_type = $request->reptype;

    // if($startDate==$endDate){
    //   if(config('settings.ncal')==0){
    //     $date = $startDate;
    //   }else{
    //     $date = getDeltaDateForReport($startDate);
    //   }
    // }else{
    //   if(config('settings.ncal')==0){
    //     $date = $startDate.'-'.$endDate;
    //   }else{
    //     $date = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
    //   }
    // }

    $handled_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

    $return_exists = ProductReturn::where('returns.company_id', $company_id)
                      ->whereIn('returns.employee_id', $handled_employees)
                      ->whereIn('returns.client_id', $client_id)
                      ->whereBetween('returns.return_date', [$startDate, $endDate])->count();

    // $returnCount = (clone $prepQuery);
    // $return_exists = $returnCount->count();
      
    if($return_exists>0){

      // $client = Auth::user()->handleQuery('client')->select("clients.id as client_id", "clients.company_name as name", "clients.client_type")
      //             ->whereIn('clients.id', $client_id)
      //             ->orderby('clients.company_name', 'asc')
      //             ->get();
      // $party_types = PartyType::select('partytypes.id', 'partytypes.name')
      //                 ->where('company_id', $company_id)
      //                 ->get();

      // $client->map(function ($item) use ($party_types) {
      //   if(isset($item->client_type)){
      //     try{
      //       $item['client_type_name'] = $party_types->filter(function ($party_item, $party_key) use ($item) {
      //       return $party_item->id==$item->client_type;
      //       })->first()->name;
      //     }catch(\Exception $e){
      //       $item['client_type_name'] = "";
      //     }
      //   }else{
      //     $item['client_type_name'] = "Unspecified";
      //   }
      // });

      // $getReturns = (clone $prepQuery);
      // $returnsAdded = $getReturns->join('return_details', 'return_details.return_id', 'returns.id')
      //                   ->get();
        
      // $products = Product::where('company_id', $company_id)
      //             ->orderby('category_id', 'DESC')
      //             ->get();

      // $brands = Brand::select('brands.id', 'brands.name')
      //             ->where('company_id', $company_id)
      //             ->orderby('name', 'ASC')
      //             ->get()->toArray();
      // $unspecified_brand["id"] = 0;
      // $unspecified_brand["name"] = "Unspecified";
      // array_push($brands, $unspecified_brand);

      // $categories = Category::select('categories.id', 'categories.name')
      //                 ->where('company_id', $company_id)
      //                 ->orderby('name', 'ASC')
      //                 ->get()->toArray();
      // $unspecified_category["id"] = 0;
      // $unspecified_category["name"] = "Unspecified";
      // array_push($categories, $unspecified_category);

      // if(count($client_id)==1){
      //   $name = str_replace(" ","",getClient($client_id)->company_name);
      // }else{
      //   $name = "Multiple_Party";
      // }
      // $time = time();
      // $type = 'xlsx';
      if($report_type == "brand"){
        $cat = "By Brand";
      //   $filename_download = $name."Returns_Reports_By_Brand_".$date;
      //   $data_sheet = $this->getBrandExcelReport($brands, $products, $client, $returnsAdded, $repKey="returns");

      //   $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');


      }else if($report_type == "category"){
        $cat = "By Category";
      //   $filename_download = $name."Returns_Reports_By_Category_".$date;
      //   $data_sheet = $this->getCategoryExcelReport($categories, $products, $client, $returnsAdded, $repKey="returns");

      //   $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');


      }else if($report_type == "consolidated"){
        $cat = "Consolidated";
      //   $filename_download = $name."Returns_Brand_Reports_Cosnolidated_".$date;
      //   $data_sheet = $this->getConsolidatedExcelReport($products, $client, $returnsAdded, $repKey="returns");

      //   $exportData['Consolidated'] = $data_sheet;
      //   $_excel = Excel::store(new MultiSheetExport($exportData), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');

      } 
      $msg['msg'] = "Generated";
      $report = new GenerateReport;
      $report->company_id = $company_id;
      $report->filename = NULL; //urlencode($filename_download.'.xlsx');
      $report->report_type = 'Return Report';
      $report->report_cat = $cat;
      $report->party_id = json_encode($client_id);
      $report->employee_id = NULL;
      $report->start_date = $startDate;
      $report->end_date = $endDate;
      $report->generated_by = Auth::user()->EmployeeId();
      $report->download_link = NULL; // 'http://'.$_SERVER['HTTP_HOST'].'/cms/storage/excel/reports/prodReturnReports/'.urlencode($filename_download.$time).'.xlsx';
      $report->save();
      // $msg['link'] = NULL;
      // $msg['status'] = 200;
      Session::flash("success", "Your Report will be generated soon.");

      return back();
      
    }else{
      // $msg['msg'] = "No Records";
      // $msg['link'] = NULL;
      // $msg['status'] = 203;
      Session::flash("warning", "There are no records between selected dates.");

      return back();
      
    }
    return $msg;
  }

  public function returnreportsDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'report_type',
      2 => 'date_range',
      3 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
              ->where('report_type', '=', 'Return Report')
              ->whereIn('generated_by', $handledEmployees)
              ->whereNotNull('report_cat')
              ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){
        if(count(json_decode($reports->party_id))>1){
          $name_content = "Multiple Party -- "; 
        }else{
          $gtClient = getClient(json_decode($reports->party_id));
          $name_content = isset($gtClient)? $gtClient->company_name . " -- ":NULL;
        } 
        $decode_party = json_decode($reports->party_id);
        if(count($decode_party)==1) $client_names = Client::whereIn('id', $decode_party)->pluck('company_name')->toArray(); 
        else $client_names = Client::whereIn('id', $decode_party)->pluck('company_name')->toArray();
        
        $name_contents = implode(',', $client_names);
        $contents = "<b>The report was generated for following parties:-</b><br/><b>Parties:-</b>"."$name_contents";

        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'. $contents .'"></span>';
        $name_content .= $reports->report_type . " -- " . $reports->report_cat . $spanContent; 

        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $tooltip_content = "$reports->tooltip_content";
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';

        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['report_type'] = $name_content;
        $nestedData['date_range'] = $date_range;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }

    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }

  public function barplotreturnsreport(Request $request){
    $company_id = config('settings.company_id');
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $clients = json_decode($request->party_id);
    
    $productReturns = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                        ->where('returns.company_id', $company_id)
                        ->whereBetween('returns.return_date', [$startDate, $endDate])
                        ->whereIn('returns.client_id', $clients)
                        ->leftjoin('products', 'return_details.product_id', 'products.id')
                        ->leftjoin('product_variants', 'return_details.variant_id', 'product_variants.id')
                        ->select(DB::raw("SUM(return_details.quantity) as sum"), "return_details.product_id", "return_details.variant_id", "products.product_name as prod_name", "product_variants.variant as var_name",  "return_details.unit_name", DB::raw("(CASE WHEN ISNULL(product_variants.variant) THEN CONCAT(products.product_name, '') ELSE CONCAT(products.product_name, ', ', product_variants.variant) END) as product_details"))
                        ->groupBy('return_details.product_id', 'return_details.variant_id')
                        ->orderBy(DB::raw("SUM(return_details.quantity)"), "desc")
                        ->take(10)->get()->toArray();

    $sum = array_column($productReturns, 'sum');
    $units = array_column($productReturns, 'unit_name');
    $product_id = array_column($productReturns, 'product_id');
    $variant_id = array_column($productReturns, 'variant_id');

    $product_name = array_column($productReturns, 'product_details');

    $chartDetails['sum'] = json_encode($sum, JSON_NUMERIC_CHECK);
    $chartDetails['product_names'] = $product_name;
    $chartDetails['units'] = $units;

    return $chartDetails;
  }

  public function piplotreturnsreport(Request $request){
    $company_id = config('settings.company_id');
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $clients = json_decode($request->party_id);

    $productReturns = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                        ->where('returns.company_id', $company_id)
                        ->whereBetween('returns.return_date', [$startDate, $endDate])
                        ->whereIn('returns.client_id', $clients)
                        ->leftjoin('products', 'return_details.product_id', 'products.id')
                        ->leftjoin('product_variants', 'return_details.variant_id', 'product_variants.id');

    $piePlotReasonsData = (clone $productReturns);
    $piePlotReasonsData = $piePlotReasonsData->leftJoin('returnreasons', 'returnreasons.id', 'return_details.reason')->select('returnreasons.name as name', DB::raw("CAST(COUNT(return_details.reason) as SIGNED) as y"),'returnreasons.name as drilldown', 'return_details.reason as reasonId')
                        ->groupBy('return_details.reason')
                        ->orderBy(DB::raw("COUNT(return_details.reason)"), "desc")
                        ->get()->toArray();

    $drillDownReasonData = array();
    foreach($piePlotReasonsData as $reasonGroupedData){
      $reasonDrillDownData = array();
      $getDetailsSum = (clone $productReturns)->where('return_details.reason', $reasonGroupedData['reasonId'])
                        ->groupBy('return_details.product_id', 'return_details.variant_id', 'return_details.reason' )
                        ->get([ DB::raw("(CASE WHEN ISNULL(product_variants.variant) THEN CONCAT(products.product_name, ' (', SUM(return_details.quantity), ' ', return_details.unit_name,')') ELSE CONCAT(products.product_name, ', ', product_variants.variant,' (', SUM(return_details.quantity), ' ',return_details.unit_name,') ') END) as product_name"), DB::raw("COUNT(return_details.product_id) as sum") ])->toArray();

      $reasonDrillDownData['name'] = $reasonGroupedData['drilldown'];
      $reasonDrillDownData['id'] = $reasonGroupedData['drilldown'];
      $reasonDrillDownData['data'] = array();

      foreach($getDetailsSum as $getDetailSum){
        $reasonProductDetails = array();
        array_push($reasonProductDetails, $getDetailSum['product_name'], (int)$getDetailSum['sum']);

        array_push($reasonDrillDownData['data'], $reasonProductDetails);
      }
      
      array_push($drillDownReasonData, $reasonDrillDownData);
    }

    $data['productReturnsReasonCountQuery'] = json_encode($piePlotReasonsData, JSON_NUMERIC_CHECK);
    $data['productReturnsDetailsCountQuery'] = json_encode($drillDownReasonData, JSON_NUMERIC_CHECK);

    return $data;
  }

  public function productpartywisereturnsreport(Request $request)
  {
    $columns = array(
      0 => 'product_name',
      1 => 'quantity',
    );
    $company_id = config('settings.company_id');
    $start = $request->input('start');
    $limit = $request->input('length');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $clients = $request->party_id;

    if($order=="quantity"){
      $order = DB::raw("SUM(return_details.quantity)");
    }

    $prepQuery = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                  ->where('returns.company_id', $company_id)
                  ->whereBetween('returns.return_date', [$startDate, $endDate])
                  ->whereIn('returns.client_id', $clients)
                  ->leftjoin('products', 'return_details.product_id', 'products.id')
                  ->select('return_details.product_id', 'products.product_name', DB::raw("SUM(return_details.quantity) as quantity"), DB::raw('count(*) as totalNumber'))
                  ->groupBy('return_details.product_id');
    $totalData = (clone $prepQuery)->get()->count();
    $data = array();
    
    if (empty($request->input('search.value'))) {
        $totalFiltered = (clone $prepQuery)->get()->count();
        $products = $prepQuery
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();
    } elseif (!(empty($request->input('search.value')))) {
        $search = $request->input('search.value');

        $productsSearchQuery = $prepQuery
                            ->where(function ($query) use ($search) {
                              $query->orWhere('products.product_name', 'LIKE', "%{$search}%");
                            });
        $totalFiltered = (clone $productsSearchQuery)->get()->count();
        $products =  $productsSearchQuery
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order, $dir)
                      ->get();
    }

    if (!empty($products)) {
      foreach ($products as $product) {
          $nestedData['product_name'] = $product->product_name;
          $clientIds = json_encode($clients);
          $nestedData['quantity'] = "<a  href='#' class='detailView' data-product_name='{$nestedData['product_name']}' data-product_id='{$product->product_id}' data-startdate='{$startDate}' data-enddate='{$endDate}' data-clientids='{$clientIds}'>{$product->quantity}</a>";

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

  public function getDetailView(Request $request){
    $company_id = config('settings.company_id');
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $clients = json_decode($request->clientIds);
    $productId = $request->productId;
    $prepQuery = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                  ->leftJoin('returnreasons', 'returnreasons.id', 'return_details.reason')
                  ->leftJoin('products', 'products.id', 'return_details.product_id')
                  ->where('returns.company_id', $company_id)
                  ->where('return_details.product_id', $productId)
                  ->whereBetween('returns.return_date', [$startDate, $endDate])
                  ->whereIn('returns.client_id', $clients)
                  ->leftJoin('clients', 'returns.client_id', 'clients.id')
                  ->groupBy('returns.client_id', 'returns.return_date', 'return_details.variant_id', 'return_details.reason')->get(['products.variant_flag', 'return_details.variant_name', 'clients.company_name', 'returns.return_date', DB::raw("SUM(return_details.quantity) as quantity"), 'returnreasons.name as reason', 'return_details.unit_name']);
    $flag = $prepQuery->first()->variant_flag;
    $prepView = View::make('company.returnsreports.detailview', compact('prepQuery', 'flag'))->render();
    $data = array();
    $data['view'] = $prepView;
    return $data;
  }

  public function custompdfdexport(Request $request){
    $getExportData = json_decode($request->exportedData);
    $columns = json_decode($request->columns);
    $properties = json_decode($request->properties);
    $pageTitle = $request->pageTitle;
    $reportName = $request->reportName;
    if($reportName == "stockreport" || $reportName == "party-wise-returns" || $reportName == "returnsDetailView"){
      $paperOrientation = "portrait";
    }else{
      $paperOrientation = "landscape";
    }
    //set_time_limit(300);
    ini_set("memory_limit", "256M");
    $pdf = PDF::loadView('company.newreports.exportpdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', $paperOrientation);
    $download = $pdf->download($pageTitle.'.pdf');
    return $download;
  }

  public function partyDetailsReport(Request $request){
    if(config('settings.party')==1 && (Auth::user()->can('partydet_rep-view'))){
      $company_id = config('settings.company_id');
      $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
      $parties = Auth::user()->handleQuery('client')->select('id','company_name','client_type')->orderBy('company_name','asc')->get()->toJson();
      $columns = array(); 
      if(config('settings.orders')==1){
        if(Auth::user()->can('order-view')){
          $columns['ord'] = 'Orders';
        }
      }
      if(config('settings.zero_orders')==1){
        if(Auth::user()->can('zeroorder-view')){
          $columns['zo'] = 'Zero Orders';
        }
      }
      if(config('settings.orders')==1  && config('settings.zero_orders')==1){
        if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
          $columns['tc'] = 'Total Calls';
        }
      }
      if(config('settings.orders')==1){
        if(Auth::user()->can('order-view')){
          if(config('settings.order_with_amt')==0){
            $columns['ov'] = 'Order Value';
          }
        }
      }
      if(config('settings.collections')==1){
        if(Auth::user()->can('collection-view')){
          $columns['coll'] = 'Collection';
        }
      }
      // if(config('settings.collections')==1){
      //   if(Auth::user()->can('collection-view')){
      //     $columns['ch2dep'] = 'Cheques to be Deposited';
      //   }
      // }
      if(config('settings.visit_module')==1){
        if(Auth::user()->can('PartyVisit-view')){
          $columns['tvst'] = 'No. of Visits';
        }
      }
      if(config('settings.visit_module')==1){
        if(Auth::user()->can('PartyVisit-view')){
          $columns['ttmvist'] = 'Time spent on Visits';
        }
      }
      if(config('settings.product')==1 && config('settings.orders')==1){
        if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
          $columns['psld'] = 'Total Products Sold';
        }
      }
      if(config('settings.orders')==1 && config('settings.zero_orders')==1){
        if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
          $columns['ectc'] = 'EC:TC(Productivity %)';
        }
      }
      if(config('settings.orders')==1){
        if(Auth::user()->can('order-view')){
          $columns['lod'] = 'Last Order Date';
        }
      }
      if(config('settings.notes')==1){
        if(Auth::user()->can('note-view')){
          $columns['nts'] = 'Latest note';
          $columns['nts_cby'] = 'Last Note Created By';
          $columns['nts_con'] = 'Last Note Created On';
        }
      }
      if(config('settings.activities')==1){
        if(Auth::user()->can('activity-view')){
          $columns['lca'] = 'Last Completed Activity';
          $columns['lacb'] = 'Last Activity completed By';
          $columns['lcat'] = 'Last Activity Completed Time';
          $columns['na'] = 'Next Activity';
          $columns['nat'] = 'Next Activity Time';
        }
      } 
      $columns['lact'] = 'Last Action Date';
      $columns['ccby'] = 'Party Created By';
      $columns['ccon'] = 'Party Created On';
      return view('company.newreports.partydetailsreports', compact('parties','partytypes','columns'));
    }else{
      return redirect()->route('company.admin.home',['domain' => domain()])->with(['error'=>'You don\'t have sufficient permission to view this content. ']);
    }
  }

  public function genPartyDetailsReport(Request $request){
    $company_id = config('settings.company_id');
    $type = 'xlsx';    
    $partyIDs = $request->partyids;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $columns = $request->colids;
    if(empty($partyIDs) || empty($startDate) || empty($endDate) || empty($columns)){
      $msg = "Please select necessary fields.";
      Session::flash("warning", $msg);
      return back();
    }
    $partyIDs = explode(',',$partyIDs);
    $columnSelected = explode(',',$columns);
    $module_permission = array();  
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('ord',$columnSelected)){
          array_push($module_permission,'ord');
        }
      }
    }
    if(config('settings.zero_orders')==1){
      if(Auth::user()->can('zeroorder-view')){
        if(in_array('zo',$columnSelected)){
          array_push($module_permission,'zo');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('tc',$columnSelected)){
          array_push($module_permission,'tc');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('ov',$columnSelected)){
          array_push($module_permission,'ov');
        }
      }
    }
    if(config('settings.collections')==1){
      if(Auth::user()->can('collection-view')){
        if(in_array('coll',$columnSelected)){
          array_push($module_permission,'coll');
        }
      }
    }
    if(config('settings.collections')==1){
      if(Auth::user()->can('collection-view')){
        if(in_array('ch2dep',$columnSelected)){
          array_push($module_permission,'ch2dep');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('PartyVisit-view')){
        if(in_array('tvst',$columnSelected)){
          array_push($module_permission,'tvst');
        }
      }
    }
    if(config('settings.visit_module')==1){
      if(Auth::user()->can('PartyVisit-view')){
        if(in_array('ttmvist',$columnSelected)){
          array_push($module_permission,'ttmvist');
        }
      }
    }
    if(config('settings.product')==1 && config('settings.orders')==1){
      if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
        if(in_array('psld',$columnSelected)){
          array_push($module_permission,'psld');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('ectc',$columnSelected)){
          array_push($module_permission,'ectc');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('lod',$columnSelected)){
          array_push($module_permission,'lod');
        }
      }
    }
    if(config('settings.notes')==1){
      if(Auth::user()->can('note-view')){
        if(in_array('nts',$columnSelected)){
          array_push($module_permission,'nts');
        }
        if(in_array('nts_cby',$columnSelected)){
          array_push($module_permission,'nts_cby');
        }
        if(in_array('nts_con',$columnSelected)){
          array_push($module_permission,'nts_con');
        }
      }
    }
    if(config('settings.activities')==1){
      if(Auth::user()->can('activity-view')){
        if(in_array('lca',$columnSelected)){
          array_push($module_permission,'lca');
        }
        if(in_array('lacb',$columnSelected)){
          array_push($module_permission,'lacb');
        }
        if(in_array('lcat',$columnSelected)){
          array_push($module_permission,'lcat');
        }
        if(in_array('na',$columnSelected)){
          array_push($module_permission,'na');
        }
        if(in_array('nat',$columnSelected)){
          array_push($module_permission,'nat');
        }
      }
    }
    if(in_array('lact',$columnSelected)){
      array_push($module_permission,'lact');
    }
    if(in_array('ccby',$columnSelected)){
      array_push($module_permission,'ccby');
    }
    if(in_array('ccon',$columnSelected)){
      array_push($module_permission,'ccon');
    }
    

    $report = new GenerateReport;
    $report->company_id = config('settings.company_id');
    $report->report_type = 'partydetails_report';
    $report->report_cat = 'partydetails_report';
    $report->party_id = json_encode($partyIDs);
    $report->employee_id = NULL;
    $report->module_access = json_encode($module_permission);
    $report->date_range = $startDate.'-'.$endDate;
    $report->start_date = $startDate;
    $report->end_date = $endDate;
    $report->generated_by = Auth::user()->EmployeeId();
    $report->save();
    Session::flash("success", "Your Report will be generated soon.");
    return back();
  }
  
  public function genPartyDetailsReportDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'date_range',
      2 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'partydetails_report')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){
        $decode_party = json_decode($reports->party_id, true);
        $company_names = !empty($decode_party)?Client::whereIn('id', $decode_party)->pluck('company_name')->toArray():"";
        if(!empty($company_names)) $partie_companies = implode(',', $company_names);
        else $partie_companies = "";
        if($reports->employee_id==NULL && $reports->party_id!=NULL){
          $content = "<b>The report was generated for following parties:-</b><br/><b>Parties:-</b>{$partie_companies}<br/>";
        }else{
          $content = "<b>Parties:-</b> None Selectced <br/>";
        }
        $reports->content =  $content;
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';


        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $employee = getEmployee($reports->employee_id);
        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['date_range'] = $date_range.' '.$spanContent;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }

    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 
  }

  public function employeeDetailsReport(Request $request){
    if((Auth::user()->can('employee-view')) && (Auth::user()->can('employeedet_rep-view'))){
      $company_id = config('settings.company_id');
      $employee = Auth::user()->handleQuery('employee')->where('company_id', $company_id)->pluck('name', 'id')->toArray();
      $columns = array();  
      if(config('settings.beat')==1){
        if(Auth::user()->can('beat-plan-view')){
          $columns['stc'] = 'Target Calls';
        }
      }
      if(config('settings.orders')==1  && config('settings.zero_orders')==1){
        if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
          $columns['tc'] = 'Total Calls';
        }
      }
      if(config('settings.orders')==1){
        if(Auth::user()->can('order-view')){
          $columns['to'] = 'Orders';
        }
      }
      if(config('settings.zero_orders')==1){
        if(Auth::user()->can('zeroorder-view')){
          $columns['zo'] = 'Zero Orders';
        }
      }
      if(config('settings.orders')==1){
        if(Auth::user()->can('order-view')){
          if(config('settings.order_with_amt')==0){
            $columns['ov'] = 'Order Value';
          }
        }
      }
      if(config('settings.collections')==1){
        if(Auth::user()->can('collection-view')){
          $columns['coll'] = 'Collection';
        }
      }
      // if(config('settings.collections')==1){
      //   if(Auth::user()->can('collection-view')){
      //     $columns['ch2dep'] = 'Cheques to be Deposited';
      //   }
      // }
      if(config('settings.visit_module')==1){
        if(Auth::user()->can('PartyVisit-view')){
          $columns['tvst'] = 'Total Visits';
        }
      }
      if(config('settings.visit_module')==1){
        if(Auth::user()->can('PartyVisit-view')){
          $columns['ttmvist'] = 'Time spent on Visits';
        }
      }
      if(config('settings.product')==1 && config('settings.orders')==1){
        if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
          $columns['psld'] = 'Total Products Sold';
        }
      }
      if(config('settings.orders')==1 && config('settings.zero_orders')==1){
        if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view')){
          $columns['ectc'] = 'EC:TC(Productivity %)';
        }
      }
      if(config('settings.monthly_attendance')==1){
        if(Auth::user()->can('monthly-attendance-view')){
          $columns['prstdays'] = 'Present Days';
        }
      }
      if(config('settings.leaves')==1){
        if(Auth::user()->can('leave-view')){
          $columns['lvs'] = 'Leaves';
        }
      }
      if(config('settings.party')==1){
        if(Auth::user()->can('party-view')){
          $columns['newprt'] = 'New Parties Added';
        }
      }
      return view('company.newreports.employeedetailsreport', compact('employee','columns'));
    }else{
      return redirect()->route('company.admin.home',['domain' => domain()])->with(['error'=>'You don\'t have sufficient permission to view this content. ']);
    }
  }

  public function genEmployeeDetailsReport(Request $request){
    $company_id = config('settings.company_id');
    $type = 'xlsx';    
    $empIDs = $request->empids;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $columns = $request->colids;
    if(empty($empIDs) || empty($startDate) || empty($endDate) || empty($columns)){
      $msg = "Please select necessary fields.";
      Session::flash("warning", $msg);
      return back();
    }
    $employeeIDS = explode(',',$empIDs);
    $columnSelected = explode(',',$columns);
    $module_permission = array(); 
    if(config('settings.beat')==1){ 
      if(Auth::user()->can('beat-plan-view')){
        if(in_array('stc',$columnSelected)){
          array_push($module_permission,'stc');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('tc',$columnSelected)){
          array_push($module_permission,'tc');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('to',$columnSelected)){
          array_push($module_permission,'to');
        }
      }
    }
    if(config('settings.zero_orders')==1){
      if(Auth::user()->can('zeroorder-view')){
        if(in_array('zo',$columnSelected)){
          array_push($module_permission,'zo');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('ov',$columnSelected)){
          array_push($module_permission,'ov');
        }
      }
    }
    if(config('settings.collections')==1){
      if(Auth::user()->can('collection-view')){
        if(in_array('coll',$columnSelected)){
          array_push($module_permission,'coll');
        }
      }
    }
    if(config('settings.collections')==1){
      if(Auth::user()->can('collection-view')){
        if(in_array('ch2dep',$columnSelected)){
          array_push($module_permission,'ch2dep');
        }
      }
    }
    if(config('settings.visit_module')==1){
      if(Auth::user()->can('PartyVisit-view')){
        if(in_array('tvst',$columnSelected)){
          array_push($module_permission,'tvst');
        }
      }
    }
    if(config('settings.visit_module')==1){
      if(Auth::user()->can('PartyVisit-view')){
        if(in_array('ttmvist',$columnSelected)){
          array_push($module_permission,'ttmvist');
        }
      }
    }
    if(config('settings.orders')==1 && config('settings.product')==1){
      if(Auth::user()->can('product-view') && Auth::user()->can('order-view')){
        if(in_array('psld',$columnSelected)){
          array_push($module_permission,'psld');
        }
      }
    }
    if(config('settings.orders')==1){
      if(Auth::user()->can('order-view')){
        if(in_array('ectc',$columnSelected)){
          array_push($module_permission,'ectc');
        }
      }
    }
    if(config('settings.monthly_attendance')==1){
      if(Auth::user()->can('monthly-attendance-view')){
        if(in_array('prstdays',$columnSelected)){
          array_push($module_permission,'prstdays');
        }
      }
    }
    if(config('settings.leaves')==1){
      if(Auth::user()->can('leave-view')){
        if(in_array('lvs',$columnSelected)){
          array_push($module_permission,'lvs');        
        }
      }
    }
    if(config('settings.party')==1){
      if(Auth::user()->can('party-view')){
        if(in_array('newprt',$columnSelected)){
          array_push($module_permission,'newprt');        
        }
      }
    }

    $report = new GenerateReport;
    $report->company_id = config('settings.company_id');
    $report->report_type = 'employeedetails_report';
    $report->report_cat = 'employeedetails_report';
    $report->party_id = NULL;
    $report->employee_id = json_encode($employeeIDS);
    $report->module_access = json_encode($module_permission);
    $report->start_date = $startDate;
    $report->end_date = $endDate;
    $report->date_range = $startDate.'-'.$endDate;
    $report->generated_by = Auth::user()->EmployeeId();
    $report->save();
    Session::flash("success", "Your Report will be generated soon.");
    return back();
  }

  public function genEmployeeDetailsReportDT(Request $request){
    $columns = array( 
      0 => 'date_generated', 
      1 => 'date_range',
      2 => 'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'salesman_name') $order = "employee_id"; 
    elseif($order == 'report_type') $order = "report_cat"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'employeedetails_report')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){

        $decode_salesman = json_decode($reports->employee_id, true);
        $employee_names = !empty($decode_salesman)?Employee::whereIn('id', $decode_salesman)->pluck('name')->toArray():"";
        if(!empty($employee_names)) $salesmans = implode(',', $employee_names);
        else $salesmans = "";
        if($reports->party_id==NULL && $reports->employee_id!=NULL){
          $content = "<b>The report was generated for following Salesman:-</b><br/><b>Salesman:-</b>{$salesmans}<br/>";
        }else{
          $content = "<b>Salesman:-</b> None Selectced <br/>";
        }
        $reports->content =  $content;
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';


        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $employee = getEmployee($reports->employee_id);
        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        $nestedData['date_range'] = $date_range.' '.$spanContent;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }

    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );
    return json_encode($json_data); 
  }

  public function productOrderDetailsReport(){
    if(config('settings.orders')==0 || !(Auth::user()->can('product_order_detail_report-view'))){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');
    $accessible_party_types = PartyType::AccessiblePartyTypes($company_id, array());
    $partytypes = $accessible_party_types['accessible_party_types'];

    $parties = Auth::user()->handleQuery('client')->whereIn('client_type', $accessible_party_types['accessible_party_types_id'])
                ->select('id','company_name','client_type')
                ->orderBy('company_name','asc')->get()->toJson();
    
    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                    ->pluck('title', 'id')
                    ->toArray();

    $client_order_approval = ClientSetting::whereCompanyId($company_id)->pluck('order_approval')->first();
    
    return view('company.newreports.productorderdetails',compact('partytypes','parties','order_statuses','client_order_approval'));
  }


  public function generateOrderDetailsReport(Request $request){
    $company_id = config('settings.company_id');
    
    $customMessages = [
      'start_date.required' => 'Field is required',
      'start_date.date' => 'Invalid date format.',
      'end_date.date' => 'Invalid date format.',
      'end_date.required' => 'Field is required',
      'party_ids.required' => 'Field is required',
      'order_status_select.required' => 'Field is required',
    ];
    $this->validate($request, [
      'start_date' => 'required|date:Y-m-d', 
      'end_date' => 'required|date:Y-m-d',
      'party_ids' => 'required',
      'order_status_select' => 'required'
    ], $customMessages);


    $startDate = $request->start_date;
    $endDate = $request->end_date;
    if(empty(json_decode($request->party_ids))){
      throw ValidationException([
        'party_ids' => 'Field is required.',
      ]);
    }
    $party_ids = json_decode($request->party_ids);
    $selected_statuses = $request->order_status_select;
    
    $type = 'xlsx';

    $ordersClients = Order::where('orders.company_id', $company_id)
                ->whereBetween('orders.order_date', [$startDate, $endDate])
                ->whereIn('orders.delivery_status_id', $selected_statuses)
                ->whereIn('orders.client_id', $party_ids)                             
                ->distinct('client_id')->pluck('client_id')->toArray();
    
    if(count($ordersClients)<=0){
      Session::flash('warning', 'There are no records between selected dates.');
      return back();
    }

    $report = new GenerateReport;
    $report->company_id = $company_id;
    $report->filename = NULL;
    $report->report_type = 'OrderDetails';
    $report->report_cat = NULL;
    $report->party_id = json_encode($ordersClients);
    $report->employee_id = NULL;
    $report->order_status = json_encode($selected_statuses);
    $report->start_date = $request->start_date;
    $report->end_date = $request->end_date;
    $report->generated_by = Auth::user()->EmployeeId();
    $report->download_link = NULL;
    $report->include_dispatch_detail = $request->include_dispatch_detail == "1" ? 1 : 0;
    $report->save();

    Session::flash('success', 'Your Report will be generated soon.');
    return back();
  }

  public function orderDetailsReportDT(Request $request){
    $columns = array( 
      'date_generated', 
      'date_range',
      'action',
    );
    $company_id = config('settings.company_id');
    $ncal = config('settings.ncal');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    if($order == 'date_generated') $order = "created_at"; 
    elseif($order == 'date_range') $order = "start_date"; 
    $dir = $request->input('order.0.dir');
    $data = array();

    $handledEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $query = GenerateReport::where('company_id', $company_id)
                        ->where('report_type', '=', 'OrderDetails')
                        ->whereIn('generated_by', $handledEmployees)
                        ->orderby($order, $dir);
    $totalData =  $query->count();
    $totalFiltered = $totalData;
    
    $generated_reports = $query
                        ->offset($start)
                        ->limit($limit)
                        ->get();
    if ($generated_reports->first()) {
      $i = $start;
    
      foreach ($generated_reports as $reports){

        $decode_party = json_decode($reports->party_id, true);
        $company_names = !empty($decode_party)?Client::whereIn('id', $decode_party)->pluck('name')->toArray():"";
        if(!empty($company_names)) $partie_companies = implode(',', $company_names);
        else $partie_companies = "";
        
        
        $content = "<b>The report was generated for following parties:-</b><br/><b>Parties:-</b>{$partie_companies}<br/>";
        $reports->content = "Product Order Details Report" ." " . $content;
        
        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date)
            $reports->date = getDeltaDateFormat($reports->start_date);
          else
            $reports->date = getDeltaDateFormat($reports->start_date). 'to' . getDeltaDateFormat($reports->end_date);
        } else{
          $reports->date = $reports->date_range;
        }

        if(isset($reports->start_date) && isset($reports->end_date)){
          if($reports->start_date == $reports->end_date) $date_range = getDeltaDate($reports->start_date);
          else $date_range = getDeltaDate($reports->start_date) . " to " . getDeltaDate($reports->end_date);
        }else{
          $date_range = $reports->date_range;
        }

        if(!empty($reports->download_link)){
          $action = "<a href='{$reports->download_link}' id='download_button' download='{$reports->filename}'><i class='fa fa-download' aria-hidden='true'></i></a>";
        }
        else{
          if($reports->processing==1) $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Processing</a>";
          elseif($reports->processing==3) $action = "No Record";
          else $action = "<a href='#'><i class='fa fa-spinner fa-pulse fa-fw'></i>Pending</a>";
        }
        $tooltip_content = "$reports->tooltip_content";
        $spanContent = '<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="'.$reports->content.'"></span>';

        $nestedData['date_generated'] = getDeltaDate($reports->created_at->format('Y-m-d'));
        // $nestedData['report_type'] = strtoupper($reports->report_type) . $spanContent;
        $nestedData['date_range'] = $date_range;
        $nestedData['action'] = $action;
        
        $data[] = $nestedData;
      }
    }
    $next_refresh = $query->where(function($q){
      $q->orWhere('processing', 1);
      $q->orWhere('processing', NULL);
    })->whereNull('download_link')->count();
    $json_data = array(
      "draw"            => intval($request->input('draw')),  
      "recordsTotal"    => intval($totalData),  
      "recordsFiltered" => intval($totalFiltered), 
      "data"            => $data,
      "next_refresh"    => $next_refresh
      );

    return json_encode($json_data); 

  }


 


} 