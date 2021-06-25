<?php

namespace App\Console\Commands;

use DB;
use Log;
use Auth;
use View;
use Excel;
use Storage;
use App\Beat;
use App\User;
use DateTime;
use App\Brand;
use App\Order;
use App\Stock;
use App\Client;
use DatePeriod;
use App\Manager;
use App\NoOrder;
use App\Product;
use App\Category;
use App\Employee;
use App\Location;
use DateInterval;
use App\BeatVPlan;
use App\PartyType;
use App\UnitTypes;
use Carbon\Carbon;
use App\Attendance;
use App\OrderDetails;
use App\ReturnDetail;
use App\ClientSetting;
use App\ProductReturn;
use App\GenerateReport;
use App\ProductVariant;
use App\ModuleAttribute;
use App\BeatPlansDetails;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;

class OrderReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'order:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel order report';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */

  private function getCompanySettings($company_id){
    try{
      $setting = ClientSetting::whereCompanyId($company_id)->first()->toArray();
      
      return $setting;
    }catch(\Exception $e){
      Log::error($e->getMessage());
      return 0;
    }
  }
  

  public function handle()
  {
    
    $count = GenerateReport::where('processing','1')->count();
    
    if($count<3){
      $report = GenerateReport::whereReportType('Order')
                ->whereNull('download_link')
                ->whereNull('processing')->limit(1)->first();
      if($report){          
          
        $company_id = $report->company_id;
        $type = 'xlsx';
        $today = date('Y-m-d');
        $reportid = $report->id;
        $report_type = $report->report_type;
        $report_cat = $report->report_cat;
        $parties = $report->party_id;
        $employees = $report->employee_id;
        $beats = $report->beat_id;
        $deliverystatus = $report->delivery_status_id;
        $startDate = $report->start_date;
        $endDate = $report->end_date;
        $party_salesman = $report->which_report;
        $generated_by = $report->generated_by;
        
        $report->processing = 1;
        $report->save();

        if($parties){
          $emp_party_sel = json_decode($report->party_id);
          $selected_statuses = $report->delivery_status_id;
        }
        
        if($employees){
          $emp_party_sel = json_decode($report->employee_id);
          $selected_statuses = $report->delivery_status_id;
        }

        if($beats){
          $emp_party_sel = json_decode($report->beat_id);
          $selected_statuses = $report->delivery_status_id;
        }
        
        if($deliverystatus){
          $emp_party_sel=json_decode($report->delivery_status_id);
          $selected_statuses = null;
        }

        $checkUnspecified = false;
        try{
          if($party_salesman == 'custom_salesman'){
            // $selected_statuses = $request->order_status_select;
            $searchKey = "employee_id";
            $folderType = "salesmen";
            $reportObjects = Employee::whereHas('orders', function($query) use ($startDate, $endDate) { 
              $query->whereBetween('orders.order_date', [$startDate, $endDate]);
              })->whereIn('id', $emp_party_sel)->orderBy('name', 'ASC')->get(['id', 'name']);
      
            $productCategoryHeader = array('', '', '', '', '', '', '');
            $productHeader = array('Salesman Name', 'Location','','','','','');
            $productVariantHeader = array('','From (Log In)','Time','To (Log Out)','Time','TC','PC');
            $grandTotal = array('Grand Total:-', '', '', '', '');
            $companyBeatPlans = $companyBeatPlans = BeatVPlan::join('beatplansdetails', 'beatplansdetails.beatvplan_id', 'beatvplans.id')->where('beatvplans.company_id', $company_id)->whereBetween('beatplansdetails.plandate', [$startDate, $endDate])->whereIn('beatvplans.employee_id', $emp_party_sel)->get(['beatplansdetails.client_id']);
            $salesmen_attendance = collect();
      
            if($startDate == $endDate) $salesmen_attendance = Attendance::where('company_id', $company_id)->where('adate', $startDate)->whereIn('employee_id', $emp_party_sel)->get(['employee_id', 'check_type', 'adate', 'atime','address']);
          }elseif($party_salesman == 'custom_party'){
            //$selected_statuses = $request->order_status_select;
            $searchKey = "client_id";
            $folderType = "parties";
            $reportObjects = Client::whereHas('orders', function($query) use ($startDate, $endDate) {
              $query->whereBetween('orders.order_date', [$startDate, $endDate]);
                })->whereIn('clients.id', $emp_party_sel)->orderBy('company_name', 'ASC')->get(['id', 'company_name as name', 'location']);
              $productCategoryHeader = array('', '', '', '');
              $productHeader = array('Party Name', 'Location', 'Last Order Date','Zero Orders');
              $productVariantHeader = array('','','', '');
              $grandTotal = array('Grand Total:-','');
          }elseif($party_salesman == 'beat_wise'){
            // $selected_statuses = $request->order_status_select;
            $searchKey = "client_id";
            $folderType = "beat_wise";
            $beat_lists = $emp_party_sel;
            unset($emp_party_sel);
            $reportObjects = Beat::with('clients')->whereStatus('Active')->whereIn('id', $beat_lists)->orderBy('name', 'ASC')->get(['id', 'name as name']);
            $checkUnspecified = in_array(0, $beat_lists);
            $handledClients = DB::table('handles')->where('employee_id', $report->generated_by)->pluck('client_id')->toArray();
            $client_with_beats = DB::table('beat_client')->whereIn('beat_id', $beat_lists)->whereIn('client_id', $handledClients)->pluck('client_id')->toArray();
            
            $productCategoryHeader = array('');
            $productHeader = array('Beat Name');
            $productVariantHeader = array('');
            $grandTotal = array('Grand Total:-');

            $allClients = Client::where('company_id', $company_id)->whereIn('id', $handledClients)->pluck('id')->toArray();
            $clientWithBeats = DB::table('beat_client')->whereIn('client_id', $allClients)->pluck('client_id')->toArray();
            $clientWithNoBeats = array_diff($allClients, $clientWithBeats);
            $client_with_no_beats = Client::where('company_id', $company_id)->whereIn('id', $clientWithNoBeats)->pluck('id')->toArray();
            $emp_party_sel = array_merge($client_with_beats, $client_with_no_beats);
            
            // if(!$checkUnspecified){
            //   $checkifBeatOrderExists = Order::whereIn('client_id', $client_with_beats)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
            // }elseif($checkUnspecified && count($beat_lists)==1){
            //   $checkifBeatOrderExists = Order::whereIn('client_id', $client_with_no_beats)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
            // }elseif($checkUnspecified && count($beat_lists)>1){
            //   $checkifBeatOrderExists = Order::whereIn('client_id', $emp_party_sel)->whereBetween('order_date', [$startDate, $endDate])->get()->count();
            // }
            
            // if($checkifBeatOrderExists<1){
            //   $view = null;
            //   $response['view'] = $view;
            //   $response['no_msg'] = "There are no orders between selected dates." ;
            //   return $response;
            // }

          }elseif($party_salesman == 'order_status_wise'){
            $selected_statuses = null;
            $searchKey = "delivery_status_id";
            $folderType = "order status";
            $order_prefix = $this->getCompanySettings($company_id)['order_prefix'];
            $reportObjects = ModuleAttribute::whereCompanyId($company_id)
                            ->whereIn('id', $emp_party_sel)
                            ->get(['title as name', 'id']);
            $checkUnspecified = false;
            $productCategoryHeader = array('', '', '', '', '', '');
            $productHeader = array('Order No.', 'Party Name', 'Party Location', 'Employee Name', 'Order Notes', 'Order Status');
            $productVariantHeader = array('', '', '', '', '', '');
            $grandTotal = array('Grand Total:-', '', '', '', '', '');
          }

          if($reportObjects->count() > 0 || $checkUnspecified){
            if($startDate == $endDate){
              $ndate = $date = $startDate;
              if($this->getCompanySettings($company_id)['ncal']==1){
                $ndate = getDeltaDateForReport($startDate);
              }
            }else{
              $ndate = $date = $startDate . 'to' . $endDate;
              if($this->getCompanySettings($company_id)['ncal']==1){
                $ndate = getDeltaDateForReport($startDate).'_to_'.getDeltaDateForReport($endDate);
              }
            }
            
            $companyOrders = Order::join('orderproducts', 'orderproducts.order_id',  'orders.id')
                                ->whereIn('orders.'.$searchKey, $emp_party_sel)
                                ->where(function($q) use($party_salesman,$company_id,$generated_by){
                                  if($party_salesman == 'order_status_wise'){
                                    //   $handed_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
                                    //   $handed_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
                                    $handed_clients = DB::table('handles')
                                                ->where('employee_id', $generated_by)
                                                ->where('handles.company_id', $company_id)
                                                ->pluck('client_id')->toArray();
                                                $user=new User;
                                    $handed_employees = $user->getChainUsers($generated_by);
                                      $q->whereIn('orders.client_id', $handed_clients);
                                      $q->whereIn('orders.employee_id', $handed_employees);
                                  }
                                })->where(function($order_status_filter) use($selected_statuses, $party_salesman){
                                  if($party_salesman != 'order_status_wise' && !empty($selected_statuses)) $order_status_filter->whereIn('orders.delivery_status_id', $selected_statuses);
                                })
                                ->whereBetween('orders.order_date', [$startDate, $endDate])
                                ->whereNull('orders.deleted_at')->whereNull('orderproducts.deleted_at')
                                ->get();

            if($party_salesman == 'order_status_wise'){
              $client_ids = $companyOrders->unique('client_id')->pluck('client_id')->toArray();
              $employee_ids = $companyOrders->unique('employee_id')->pluck('employee_id')->toArray();
              $client_details = Client::whereIn('id', $client_ids)->get(['id', 'company_name', 'location']);
              $employee_details = Employee::whereIn('id', $employee_ids)->pluck('name', 'id')->toArray();
            } 
            $allOrders = (clone $companyOrders);
            if($party_salesman != 'order_status_wise'){
              $companyNoOrders = NoOrder::whereIn('no_orders.'.$searchKey, $emp_party_sel)
                ->whereBetween('no_orders.date', [$startDate, $endDate])->get();
            }
            
            $brands = Brand::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'name']);
            $categories = Category::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'name']);
            $units = UnitTypes::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'symbol']);

            $companyProductVariants = Product::where('products.company_id', $company_id)
                                      ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
                                      ->orderBy('products.category_id', 'desc')
                                      ->orderBy('products.id', 'desc')
                                      ->get(['products.id as product_id','products.product_name', 'products.brand','products.category_id','products.unit as product_unit', 'products.status as product_status', 'products.variant_flag', 'product_variants.id as variant_id', 'product_variants.variant', 'product_variants.unit as variant_unit']);
      
            $newCollec = $this->getProductColletion($companyProductVariants, $companyOrders);

            unset($companyProductVariants);
            $companyProductVariants = $newCollec;
            $companyProducts = $this->getBrandCategoryUnitMapping($companyProductVariants, $brands, $categories, $units);
            $reportData = array();
      
            if($report_cat == "By Brand"){
              $rName = 'byBrand';
              $catBrandsArray = $brands->toArray();
              $storagePath = 'excel/reports/orderReports';
              $unspecifiedName = "Unspecified";
              $unspecifiedId = 0;
            }elseif($report_cat == "By Category"){
              $rName = 'byCatgory';
              $catBrandsArray = $categories->toArray();
              $storagePath = 'excel/reports/orderReports';  
              $unspecifiedName = "Unspecified";   
              $unspecifiedId = 0; 
            }elseif($report_cat == "Consolidated"){
              $rName = 'consolidated';
              $catBrandsArray = array();
              $storagePath = 'excel/reports/orderReports';
              $unspecifiedName = "Consolidated";
              $unspecifiedId = 1;
            }
            array_push($catBrandsArray, ["id"=>$unspecifiedId, "name"=>$unspecifiedName]);

            //Log::info('info', array("reportObject"=>print_r($catBrandsArray,true)));
      
            $data = array();
            $status_titles = $reportObjects->pluck('name', 'id')->toArray();
            foreach ($catBrandsArray as $catBrand) {
              $catBrand_id = $catBrand['id'];
              $catBrand_name = $catBrand['name'];
              if ($report_cat == "By Brand" || $report_cat == "By Category") {
                $catBrandProductCategoryHeader = $productCategoryHeader;
              }else{
                $catBrandProductCategoryHeader = array();
              }
              $catBrandProductHeader = $productHeader;
              $catBrandProductVariantHeader = $productVariantHeader;
              $catBrandGrandTotal = $grandTotal;
              $getOrderSumBeforeAddingVariant = null;
              if($catBrand_id != 0){
                if($report_cat == "By Brand"){
                  $catBrandProducts = $companyProducts->filter(function($companyProduct) use($catBrand_id){
                      if($companyProduct->brand == $catBrand_id){
                        return true;
                      }
                    });
                }elseif($report_cat == "By Category"){
                  $catBrandProducts = $companyProducts->filter(function($companyProduct) use($catBrand_id){
                      if($companyProduct->category_id == $catBrand_id){
                        return true;
                      }
                    });
                }else{
                  $catBrandProducts = $companyProducts;
                }
              }else{
                if($report_cat == "By Brand"){
                  $catBrandProducts = $companyProducts->filter(function($companyProduct) use($catBrand_id){
                      if(empty($companyProduct->brand)){
                        return true;
                      }
                    });
                }elseif($report_cat == "By Category"){
                  $catBrandProducts = $companyProducts->filter(function($companyProduct) use($catBrand_id){
                      if(empty($companyProduct->category_id)){
                        return true;
                      }
                    });
                }
              }
              
              if($catBrandProducts->count()>0){
                $allOrderDetails = array();
                $allOrderDetails[] = array(ucfirst($catBrand_name));
                $allOrderDetails[] = array($ndate);
                $allOrderDetails[] = array();
                $allOrderDetails["categoryHeader"] = array();
                $allOrderDetails["productHeader"] = array();
                $allOrderDetails["variantHeader"] = array();
                if($searchKey == "employee_id"){
                  $grandTotalTC = 0;
                  $grandTotalPC = 0;
                }else{
                  $last_order_placed_date = "";
                  $grandTotalZeroOrders = 0;
                }
                $grandTotalOrders = array();
                $firstReportObject = true;
          
                if($searchKey == "employee_id"){
                  foreach($reportObjects as $reportObject){
                    $id = $reportObject->id;
                    $name = $reportObject->name;
                    $totalTC = 0;
                    $totalPC = 0;
                    $dataRow = array();
                    array_push($dataRow, $name);
                    $employee_checkin_time = "";
                    $employee_checkin_address = "";
                    $employee_checkout_time = "";
                    $employee_checkout_address = "";
          
                    if($salesmen_attendance->first()){
                      $getAttendanceDetails = $this->getAttendanceDetails((clone $salesmen_attendance), $id);
        
                      if($startDate == $today){
                        if(empty($getAttendanceDetails['CheckIn'])){
                          $employee_checkin_time = "Absent";
                          $employee_checkout_time = "Absent";
                        }else{
                          $employee_checkin_time = $getAttendanceDetails['CheckIn']->atime;
                          $employee_checkin_address = $getAttendanceDetails['CheckIn']->address;
                          if(!empty($getAttendanceDetails['CheckOut'])){
                            $employee_checkout_time = $getAttendanceDetails['CheckOut']->atime;
                            $employee_checkout_address = $getAttendanceDetails['CheckOut']->address;
                          }
                        }
                      }
                    }
                
                    array_push($dataRow, $employee_checkin_time, $employee_checkin_address, $employee_checkout_time, $employee_checkout_address);
                    if(isset($companyBeatPlans)){
                      $employeePendingAndTotalCalls = $this->getEmployeePendingAndTotalCalls($id, (clone $companyOrders), (clone $companyNoOrders), (clone $companyBeatPlans));
                      $totalTC = count($employeePendingAndTotalCalls['totalCalls']);
                      $totalPC = count($employeePendingAndTotalCalls['pendingCalls']);
                      $grandTotalTC += $totalTC;
                      $grandTotalPC += $totalPC;
        
                      array_push($dataRow, "$totalTC", "$totalPC");
                    }else {
                      $totalTC = 0;
                      $totalPC = 0;
                      $grandTotalTC += $totalTC;
                      $grandTotalPC += $totalPC;
        
                      array_push($dataRow, "$totalTC", "$totalPC");
                    }
                    
                    $i = 0;
                    //Log::info('info', array("Cat_Brand_product"=>print_r($catBrandProducts,true)));
              
                    foreach($catBrandProducts as $catBrandProduct){
                      $productTotal = 0;
          
                      if($firstReportObject){
                        if($report_cat == "By Brand"){
                          if(!in_array($catBrandProduct->category_name, $catBrandProductCategoryHeader)){
                            array_push($catBrandProductCategoryHeader, $catBrandProduct->category_name);
                          }else{
                            array_push($catBrandProductCategoryHeader, "");
                          }
                        }
          
                        if(!in_array($catBrandProduct->product_name, $catBrandProductHeader)){
                          array_push($catBrandProductHeader, $catBrandProduct->product_name);
                        }else{
                          array_push($catBrandProductHeader, "");
                        }
          
                        $variantWithUnit = isset($catBrandProduct->variant)?$catBrandProduct->variant.(isset($catBrandProduct->unit_symbol)?"(".$catBrandProduct->unit_symbol.")":null):$catBrandProduct->unit_symbol;
          
                        array_push($catBrandProductVariantHeader, $variantWithUnit);
                      }
                      if(isset($catBrandProduct->variant_id)){
                        $getOrdersSum = $this->getOrdersDetails($searchKey, $id, $catBrandProduct, (clone $companyOrders));
                        array_push($dataRow, "$getOrdersSum");
          
                        if(array_key_exists($i, $grandTotalOrders)){
                          $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                        }else{
                          $grandTotalOrders[$i] = $getOrdersSum;
                        }
                      }else{
                        $getOrdersSum = $this->getOrdersDetails($searchKey, $id, $catBrandProduct, (clone $companyOrders));
                        array_push($dataRow, "$getOrdersSum");
                        
                        if(array_key_exists($i, $grandTotalOrders)){
                          $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                        }else{
                          $grandTotalOrders[$i] = $getOrdersSum;
                        }
                      } 
                      ++$i;
                    }
                    if($firstReportObject){
                      $allOrderDetails["categoryHeader"] = $catBrandProductCategoryHeader ;
                      $allOrderDetails["productHeader"] = $catBrandProductHeader;
                      $allOrderDetails["variantHeader"] = $catBrandProductVariantHeader;
                    }
                    $firstReportObject = false;
                    $allOrderDetails[] = $dataRow;
                    unset($dataRow);
                    unset($catBrandProductCategoryHeader);
                    unset($catBrandProductHeader);
                    unset($catBrandProductVariantHeader);
                  }
                  array_push($catBrandGrandTotal, "$grandTotalTC", "$grandTotalPC");
                }elseif($searchKey == "client_id"){
                  if($party_salesman == 'custom_party'){
                    foreach($reportObjects as $reportObject){
                      $id = $reportObject->id;
                      $name = $reportObject->name;
                      $location = $reportObject->location;
                      $dataRow = array();
                      array_push($dataRow, $name, $location);

                      $lastOrderPlacedDate = $this->getLastOrderPlacedDate($id, (clone $allOrders));
                      array_push($dataRow, $lastOrderPlacedDate);
          
                      $numZeroOrders = $this->getZeroOrdersCount($id, (clone $companyNoOrders));
                      $grandTotalZeroOrders += $numZeroOrders;
                      array_push($dataRow, "$numZeroOrders");
                      $i = 0;
                      foreach($catBrandProducts as $catBrandProduct){
                        $productTotal = 0;
            
                        if($firstReportObject){
                          if($report_cat == "By Brand"){
                            if(!in_array($catBrandProduct->category_name, $catBrandProductCategoryHeader)){
                              array_push($catBrandProductCategoryHeader, $catBrandProduct->category_name);
                            }else{
                              array_push($catBrandProductCategoryHeader, "");
                            }
                          }
            
                          if(!in_array($catBrandProduct->product_name, $catBrandProductHeader)){
                            array_push($catBrandProductHeader, $catBrandProduct->product_name);
                          }else{
                            array_push($catBrandProductHeader, "");
                          }
            
                          $variantWithUnit = isset($catBrandProduct->variant)?$catBrandProduct->variant.(isset($catBrandProduct->unit_symbol)?"(".$catBrandProduct->unit_symbol.")":null):$catBrandProduct->unit_symbol;
            
                          array_push($catBrandProductVariantHeader, $variantWithUnit);
                        }
                        if(isset($catBrandProduct->variant_id)){
                          $getOrdersSum = $this->getOrdersDetails($searchKey, $id, $catBrandProduct, (clone $companyOrders));
                          array_push($dataRow, "$getOrdersSum");
            
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        }else{
                          $getOrdersSum = $this->getOrdersDetails($searchKey, $id, $catBrandProduct, (clone $companyOrders));
                          array_push($dataRow, "$getOrdersSum");
                          
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        } 
                        ++$i;
                      }
                      if($firstReportObject){
                        $allOrderDetails["categoryHeader"] = $catBrandProductCategoryHeader ;
                        $allOrderDetails["productHeader"] = $catBrandProductHeader;
                        $allOrderDetails["variantHeader"] = $catBrandProductVariantHeader;
                      }
                      $firstReportObject = false;
                      $allOrderDetails[] = $dataRow;
                      unset($dataRow);
                      unset($catBrandProductCategoryHeader);
                      unset($catBrandProductHeader);
                      unset($catBrandProductVariantHeader);
                    }
                    array_push($catBrandGrandTotal, "", "$grandTotalZeroOrders");
                  }elseif($party_salesman == 'beat_wise'){
                    foreach($reportObjects as $reportObject){
                      $id = $reportObject->id;
                      $name = $reportObject->name;
                      $dataRow = array();
                      $ids = $reportObject->parties()->get()->pluck('id')->toArray();
                      array_push($dataRow, $name);
                      
                      $i = 0;
                      foreach($catBrandProducts as $catBrandProduct){
                        $productTotal = 0;
            
                        if($firstReportObject){
                          if($report_cat == "By Brand"){
                            if(!in_array($catBrandProduct->category_name, $catBrandProductCategoryHeader)){
                              array_push($catBrandProductCategoryHeader, $catBrandProduct->category_name);
                            }else{
                              array_push($catBrandProductCategoryHeader, "");
                            }
                          }
            
                          if(!in_array($catBrandProduct->product_name, $catBrandProductHeader)){
                            array_push($catBrandProductHeader, $catBrandProduct->product_name);
                          }else{
                            array_push($catBrandProductHeader, "");
                          }
            
                          $variantWithUnit = isset($catBrandProduct->variant)?$catBrandProduct->variant.(isset($catBrandProduct->unit_symbol)?"(".$catBrandProduct->unit_symbol.")":null):$catBrandProduct->unit_symbol;
            
                          array_push($catBrandProductVariantHeader, $variantWithUnit);
                        }
                        if(isset($catBrandProduct->variant_id)){
                          $getOrdersSum = (clone $companyOrders)->whereIn('client_id', $ids)->where('product_id', $catBrandProduct->product_id)->where('product_variant_id', $catBrandProduct->variant_id)->where('unit', $catBrandProduct->variant_unit)->sum('quantity');
                          array_push($dataRow, "$getOrdersSum");
            
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        }else{
                          $getOrdersSum = (clone $companyOrders)->whereIn('client_id', $ids)->where('product_id', $catBrandProduct->product_id)->where('unit', $catBrandProduct->product_unit)->sum('quantity');
                          array_push($dataRow, "$getOrdersSum");
                          
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        } 
                        ++$i;
                      }
                      if($firstReportObject){
                        $allOrderDetails["categoryHeader"] = $catBrandProductCategoryHeader ;
                        $allOrderDetails["productHeader"] = $catBrandProductHeader;
                        $allOrderDetails["variantHeader"] = $catBrandProductVariantHeader;
                      }
                      $firstReportObject = false;
                      $allOrderDetails[] = $dataRow;
                      unset($dataRow);
                      unset($catBrandProductCategoryHeader);
                      unset($catBrandProductHeader);
                      unset($catBrandProductVariantHeader);
                    }
                    if($checkUnspecified){
                      $id = "0";
                      $name = "Unspecified";
                      $dataRow = array();
                      $ids = $client_with_no_beats;
                      array_push($dataRow, $name);
                      
                      $i = 0;
                      foreach($catBrandProducts as $catBrandProduct){
                        $productTotal = 0;
            
                        if($firstReportObject){
                          if($report_cat == "By Brand"){
                            if(!in_array($catBrandProduct->category_name, $catBrandProductCategoryHeader)){
                              array_push($catBrandProductCategoryHeader, $catBrandProduct->category_name);
                            }else{
                              array_push($catBrandProductCategoryHeader, "");
                            }
                          }
            
                          if(!in_array($catBrandProduct->product_name, $catBrandProductHeader)){
                            array_push($catBrandProductHeader, $catBrandProduct->product_name);
                          }else{
                            array_push($catBrandProductHeader, "");
                          }
            
                          $variantWithUnit = isset($catBrandProduct->variant)?$catBrandProduct->variant.(isset($catBrandProduct->unit_symbol)?"(".$catBrandProduct->unit_symbol.")":null):$catBrandProduct->unit_symbol;
            
                          array_push($catBrandProductVariantHeader, $variantWithUnit);
                        }
                        if(isset($catBrandProduct->variant_id)){
                          $getOrdersSum = (clone $companyOrders)->whereIn('client_id', $ids)->where('product_id', $catBrandProduct->product_id)->where('product_variant_id', $catBrandProduct->variant_id)->where('unit', $catBrandProduct->variant_unit)->sum('quantity');
                          array_push($dataRow, "$getOrdersSum");
            
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        }else{
                          $getOrdersSum = (clone $companyOrders)->whereIn('client_id', $ids)->where('product_id', $catBrandProduct->product_id)->where('unit', $catBrandProduct->product_unit)->sum('quantity');
                          array_push($dataRow, "$getOrdersSum");
                          
                          if(array_key_exists($i, $grandTotalOrders)){
                            $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                          }else{
                            $grandTotalOrders[$i] = $getOrdersSum;
                          }
                        } 
                        ++$i;
                      }
                      if($firstReportObject){
                        $allOrderDetails["categoryHeader"] = $catBrandProductCategoryHeader ;
                        $allOrderDetails["productHeader"] = $catBrandProductHeader;
                        $allOrderDetails["variantHeader"] = $catBrandProductVariantHeader;
                      }
                      $firstReportObject = false;
                      $allOrderDetails[] = $dataRow;
                      unset($dataRow);
                      unset($catBrandProductCategoryHeader);
                      unset($catBrandProductHeader);
                      unset($catBrandProductVariantHeader);
                    }
                  }
                }elseif($searchKey == "delivery_status_id"){
                  foreach($companyOrders as $companyOrder){
                    $orderNo = $order_prefix.$companyOrder->order_no;
                    $party_details = $client_details->where('id', $companyOrder->client_id)->first();
                    $party_name = $party_details->company_name;
                    $location = $party_details->location;
                    $employee_name = $employee_details[$companyOrder->employee_id];
                    $order_notes = strip_tags($companyOrder->order_note);
                    $deivery_status = $status_titles[$companyOrder->delivery_status_id];

                    $dataRow = array();
                    array_push($dataRow, $orderNo, $party_name, $location, $employee_name, $order_notes, $deivery_status);

                    $i = 0;
                    foreach($catBrandProducts as $catBrandProduct){
                      $productTotal = 0;
          
                      if($firstReportObject){
                        if($report_cat == "By Brand"){
                          if(!in_array($catBrandProduct->category_name, $catBrandProductCategoryHeader)){
                            array_push($catBrandProductCategoryHeader, $catBrandProduct->category_name);
                          }else{
                            array_push($catBrandProductCategoryHeader, "");
                          }
                        }
          
                        if(!in_array($catBrandProduct->product_name, $catBrandProductHeader)){
                          array_push($catBrandProductHeader, $catBrandProduct->product_name);
                        }else{
                          array_push($catBrandProductHeader, "");
                        }
          
                        $variantWithUnit = isset($catBrandProduct->variant)?$catBrandProduct->variant.(isset($catBrandProduct->unit_symbol)?"(".$catBrandProduct->unit_symbol.")":null):$catBrandProduct->unit_symbol;
          
                        array_push($catBrandProductVariantHeader, $variantWithUnit);
                      }
                      if(isset($catBrandProduct->variant_id)){
                        $getOrdersSum = 0;
                        if($catBrandProduct->variant_id == $companyOrder->product_variant_id && $catBrandProduct->product_id == $companyOrder->product_id) $getOrdersSum = $companyOrder->quantity;
                        array_push($dataRow, "$getOrdersSum");
          
                        if(array_key_exists($i, $grandTotalOrders)){
                          $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                        }else{
                          $grandTotalOrders[$i] = $getOrdersSum;
                        }
                      }else{
                        $getOrdersSum = 0;
                        if($catBrandProduct->product_id == $companyOrder->product_id) $getOrdersSum = $companyOrder->quantity;
                        array_push($dataRow, "$getOrdersSum");
                        
                        if(array_key_exists($i, $grandTotalOrders)){
                          $grandTotalOrders[$i] = $grandTotalOrders[$i] + $getOrdersSum;
                        }else{
                          $grandTotalOrders[$i] = $getOrdersSum;
                        }
                      } 
                      ++$i;
                    }
                    if($firstReportObject){
                      $allOrderDetails["categoryHeader"] = $catBrandProductCategoryHeader ;
                      $allOrderDetails["productHeader"] = $catBrandProductHeader;
                      $allOrderDetails["variantHeader"] = $catBrandProductVariantHeader;
                    }
                    $firstReportObject = false;
                    $allOrderDetails[] = $dataRow;
                    unset($dataRow);
                    unset($catBrandProductCategoryHeader);
                    unset($catBrandProductHeader);
                    unset($catBrandProductVariantHeader);
                  }
                }
          
            
                $allOrderDetails[] = array_merge($catBrandGrandTotal, array_map('strval',$grandTotalOrders));
                if($searchKey == "employee_id"){
                  $allOrderDetails[] = array();
                  $allOrderDetails[] = array("*** TC:- Total Calls ***", "*** PC:- Pending Calls ***");
                }
                // Log::info('info', array("Orderdetails"=>print_r($catBrand_name,true)));
                $data[$catBrand_name] = $allOrderDetails;
              }
              // Log::info('info', array("Orderdetails"=>print_r($data,true)));
            }
        
            // Log::info('info', array("alldata"=>print_r($data,true)));
            //Log::info('info', array("reportObject"=>print_r($folderType,true)));

            if($folderType == "parties" || $folderType == "salesmen" || $folderType == "order status" ){
              if($reportObjects->count()==1){
                $name = str_replace(" ","",$reportObjects->first()->name);
                $filename_download = urlencode($name.'OrderReport_'.$rName.'_'.$ndate);
                $filename = $name.'OrderReport_'.$ndate.'_'.time();
              }else{
                $filename_download = ucfirst($folderType).'OrderReport_'.$rName.'_'.$ndate;
                $filename = ucfirst($folderType).'OrderReport_'.$ndate.'_'.time();
              }
            }elseif($folderType == "beat_wise"){
              if($reportObjects->count()==1){
                $name = str_replace(" ","",$reportObjects->first()->name);
                $filename_download = urlencode($name.'OrderReport_'.$rName.'_'.$ndate);
                $filename = $name.'OrderReport_'.$ndate.'_'.time();
              }elseif($reportObjects->count()==0 && $checkUnspecified ){
                $name = str_replace(" ","","Unspecified Beats");
                $filename_download = urlencode($name.'OrderReport_'.$rName.'_'.$ndate);
                $filename = $name.'OrderReport_'.$ndate.'_'.time();
              }else{
                $filename_download = ucfirst("Beats").'OrderReport_'.$rName.'_'.$ndate;
                $filename = ucfirst("Beats").'OrderReport_'.$ndate.'_'.time();
              }
            }

            try{
              $store = Excel::store(new MultiSheetExport($data), $filename.'.'.$type, 'orderReports');
            }catch(\Maatwebsite\Excel\Exceptions\LaravelExcelException $ex)
            {
              \Log::info("Spreadsheet requires a minimal record.");
            }
    
            if($store){

              $updatereport = GenerateReport::find($reportid);
              $updatereport->processing = 2;
              $updatereport->filename = $filename_download.'.'.$type;
              $updatereport->download_link = env('APP_URL').'storage/' .$storagePath.'/' . $filename . '.' . $type;
              $updatereport->save();
            }
          }else{
              
            $report->processing = 3;
            $report->save();
              
              
          }
          \Log::info("Order Report Generated Successfully!");
        }catch(\Exception $e){
          Log::error(
            print_r(array(
              "Order Report", 
              $e->getMessage()), 
              true)
          );
          $report->processing = 0;
          $report->save();
        }
      }
    }
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

   public function getOrdersDetails($key, $searchId, $productCollection, $orderCollection){
    if($productCollection->variant_flag == 0){
      $orderSum = $orderCollection->where($key, $searchId)->where('product_id', $productCollection->product_id)->where('product_variant_id', null)->where('unit', $productCollection->product_unit)->sum('quantity');
    }elseif($productCollection->variant_flag == 1){
      $orderSum = $orderCollection->where($key, $searchId)->where('product_id', $productCollection->product_id)->where('product_variant_id', $productCollection->variant_id)->where('unit', $productCollection->variant_unit)->sum('quantity');
    } 

    return $orderSum;
  }
  
  public function getLastOrderPlacedDate($clientId, $allOrdersCollection){
    if(!empty($allOrdersCollection)){
      $lastDate = $allOrdersCollection->where('client_id', $clientId)->max('order_date');
    }else{
      $lastDate = "";
    }

    return $lastDate?getDeltaDateForReport($lastDate):$lastDate;
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
}
