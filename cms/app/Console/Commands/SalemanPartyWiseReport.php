<?php

namespace App\Console\Commands;

use Excel;
use DateTime;
use App\Brand;
use App\Order;
use App\Stock;
use App\Client;
use DatePeriod;
use App\Product;
use App\Category;
use App\Employee;
use DateInterval;
use App\PartyType;
use App\UnitTypes;
use App\ClientSetting;
use App\GenerateReport;
use App\ProductVariant;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalemanPartyWiseReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'saleman-party-wise:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel ssaleman-party-wise report(Salesman Party-wise Order Report)';

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

  private function getDateBetween($lowerRangeDate, $higherRangeDate, $format = 'Y-m-d'){
    $dates = array();
      $current = strtotime($lowerRangeDate);
      $date2 = strtotime($higherRangeDate);
      $stepVal = '+1 day';
      while( $current <= $date2 ) {
         $dates[] = date($format, $current);
         $current = strtotime($stepVal, $current);
      }
      return $dates;
  }
  
  public function handle()
  {
    $processing_stock_reports_count = GenerateReport::where('processing','1')->count();
    
    if($processing_stock_reports_count<3){
      $processing = GenerateReport::whereReportType('DailySalesman')
                ->whereNULL('download_link')
                ->whereNULL('processing')
                ->first();
      if($processing){
        try{
          $processing->processing = 1;
          $processing->save();
          
          $company_id = $processing->company_id;
          $report_type = $processing->report_type;
          $report_cat = $processing->report_cat;
          $generated_by = $processing->generated_by;
          $type = 'xlsx';
          $today = date('Y-m-d');
          $salesman_id = $processing->employee_id;
          $order_statuses = json_decode($processing->order_status);
          $startDate = $processing->start_date;
          $endDate = $processing->end_date;
          if($startDate == $endDate){
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate;    
            }else{
              $date = getDeltaDateForReport($startDate);
            }
          }else{
            if($this->getCompanySettings($company_id)['ncal']==0){    
              $date = $startDate . ' to ' . $endDate;
            }else{
              $date = getDeltaDateForReport($startDate) . ' to ' . getDeltaDateForReport($endDate);
            }
          }
          $salesman = Employee::find($salesman_id); 
          $filename = time().'_'.$date.'_'.$salesman->name;
          $generator = Employee::find($generated_by);
          $handled_party = $generator->user->handleQuery('client')->pluck('id')->toArray();
          $parties = Client::whereIn('id', $handled_party)->select('id', 'name', 'company_name', 'address_1', 'created_by', DB::raw('CAST(created_at AS DATE) as party_added') )->get();
        
          if($report_cat == "daily"){
            $cat = array('','','','');
            $prod = array('Party Name', 'Address', 'Status','New Party?');
            $prod_variants = array('', '', '','');
          }else{
            $cat = array('','','','','');
            $prod = array('Party Name', 'Address', 'New Party?', 'Effective Calls', 'Non-Effective Calls');
            $prod_variants = array('', '', '', '', '');
          }
          $prod_variant_ids = array();
          
          $salesmanOrders = Order::whereBetween('orders.order_date', [$startDate, $endDate])
                              ->whereIn('orders.client_id', $handled_party)
                              ->whereIn('orders.delivery_status_id', $order_statuses)->get();
    
          $salesmanOrderDetails = $salesmanOrders;
          $brands = Brand::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'name']);
          $categories = Category::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'name']);
          $units = UnitTypes::where('company_id', $company_id)->orderby('name', 'ASC')->get(['id', 'symbol']);
          
          $companyProductVariants = Product::where('products.company_id', $company_id)
                                    ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
                                    ->orderBy('products.category_id', 'desc')
                                    ->orderBy('products.id', 'desc')
                                    ->get(['products.id as product_id', 'products.product_name', 'products.brand', 'products.category_id', 'products.unit as product_unit', 'products.status as product_status', 'products.variant_flag', 'product_variants.id as variant_id', 'product_variants.variant', 'product_variants.unit as variant_unit']);
            $newCollec = collect();
            $prevObj = NULL;
            foreach($companyProductVariants as $product){
              if ($product->product_status=="Inactive") {
                $checkIfOrdered = (clone $salesmanOrderDetails)->where('product_id', $product->product_id)->count();
                if($checkIfOrdered==0) continue;
              }  
              $newCollec->push($product);
              if(isset($product->variant_id)){
                if(isset($prevObj)){
                  if($prevObj->product_id!=$product->product_id){
                    $checkIfOrderExistBeforeVariant = (clone $salesmanOrderDetails)->where('product_id', $product->product_id)->where('product_variant_id', NULL)->where('unit', '!=', NULL)->where('unit', '!=', $product->product_unit)->unique('unit')->pluck('unit')->toArray();
                    if(count($checkIfOrderExistBeforeVariant)>0){
                      foreach($checkIfOrderExistBeforeVariant as $key=>$orderUnit){
                        if(isset($orderUnit) && $orderUnit!=$product->product_unit){
                          $cloned = (clone $product);
                          $cloned->variant_id = NULL;
                          $cloned->variant = "";
                          $cloned->product_unit = $orderUnit;
                          $cloned->variant_unit = $orderUnit;
                          $newCollec->push($cloned);
                          unset($cloned);
                        }
                      }
                    }
                  }
                }
                $getOrderDetails = (clone $salesmanOrderDetails)->where('product_id', $product->product_id)->where('product_variant_id', $product->variant_id)->where('unit', '!=', NULL)->where('unit', '!=', $product->variant_unit)->unique('unit')->pluck('unit')->toArray();
                if(count($getOrderDetails)>0){
                  foreach($getOrderDetails as $orderUnit){
                    $cloned = (clone $product);
                    $cloned->variant_unit = $orderUnit;
                    $newCollec->push($cloned);
                  }
                }
              }else{
                $getOrderDetails = (clone $salesmanOrderDetails)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->where('unit', '!=', $product->product_unit)->unique('unit')->pluck('unit')->toArray();
                if(count($getOrderDetails)>0){
                  foreach($getOrderDetails as $orderUnit){
                    $cloned = (clone $product);
                    $cloned->product_unit = $orderUnit;
                    $newCollec->push($cloned);
                  }
                }
              }
              unset($prevObj);
              $prevObj = $product;
            }
    
            unset($companyProductVariants);
          $companyProductVariants = $newCollec;
    
          $companyProducts = $companyProductVariants
                              ->map(function($item) use($categories, $brands, $units){
                                $item['brand_name'] = "";
                                $item['category_name'] = "Unspecified";
                                $item['unit_symbol'] = NULL;
                                $filterBrandName = (empty($item->brand))?($item['brand_name'] =  "Unspecified"):$brands->filter(function ($brand) use($item) {
                                                      if($item->brand == $brand->id){
                                                        $item['brand_name'] =  $brand->name;
                                                        return true;
                                                      }
                                                    });
                                $filterCategoryName = (empty($item->category_id))?($item['category_name'] =  "Unspecified"):$categories->filter(function ($category) use($item) {
                                                        if($item->category_id == $category->id){
                                                          $item['category_name'] =  $category->name;
                                                          return true;
                                                        }
                                                      });
                                $filterUnitName = (empty($item->variant_unit))?$units->filter(function ($unit) use($item) {
                                                      if($item->product_unit == $unit->id){
                                                        $item['unit_symbol'] =  $unit->symbol;
                                                        return true;
                                                      }
                                                    }):$units->filter(function ($unit) use($item) {
                                                      if($item->variant_unit == $unit->id){
                                                        $item['unit_symbol'] =  $unit->symbol;
                                                        return true;
                                                      }
                                                    });
                                return $item;
                              });
          foreach($companyProducts as $companyProduct){
            if(!in_array($companyProduct->category_name, $cat)) array_push($cat, $companyProduct->category_name);
            else array_push($cat, "");
            $prodBrand = !isset($companyProduct->brand_name)?$companyProduct->product_name:$companyProduct->product_name."(".$companyProduct->brand_name.")";
            if(!in_array($prodBrand, $cat)) array_push($prod, $prodBrand);
            else array_push($prod, "");
      
            $variant_name = $companyProduct->variant."(".$companyProduct->unit_symbol.")";
            array_push($prod_variants, $variant_name);
          }
          $salesman_order_clients = $salesmanOrders->unique('client_id')->pluck('client_id')->toArray();
          $salesman_order_dates = $salesmanOrders->unique('order_date')->pluck('order_date')->toArray();
      
          $salesmanNoOrders = $salesman->noorders()->whereBetween('no_orders.date', [$startDate, $endDate])->whereIn('no_orders.client_id', $handled_party)->get(); 
          $salesman_no_order_dates = $salesmanNoOrders->unique('date')->pluck('date')->toArray();
          $salesman_no_order_clients = $salesmanNoOrders->unique('client_id')->pluck('client_id')->toArray();
            
          $salesman_covered_dates = array_unique(array_merge($salesman_order_dates, $salesman_no_order_dates));
          $salesman_covered_clients = array_unique(array_merge($salesman_order_clients, $salesman_no_order_clients));
            
          if($report_cat == "daily"){
            $filename_download = 'SalesReport_'.$salesman->name.'(Dly)_'.$date;
            $after_download_name = 'SalesReport_'.$salesman->name.'(Dly)_'.$date.'_'.time();
            $allDatesList = $startDate == $endDate ? array($startDate) : $this->getDateBetween($startDate, $endDate);
            $grandTotal = array();
            foreach($allDatesList as $salesman_order_date){
              $grandTotal = array();
              $flag = false;
              if($this->getCompanySettings($company_id)['ncal']==0){
                $data[] = array($salesman_order_date);
              }else{
                $data[] = array(getDeltaDateForReport($salesman_order_date));
              }
              $effCalls = $salesmanOrders->where('order_date', $salesman_order_date)->unique('client_id')->pluck('client_id')->toArray();
              $data[] = array("Total unique effective calls:-", (string)count($effCalls));
              $noneffCalls = $salesmanNoOrders->where('date', $salesman_order_date)->unique('client_id')->pluck('client_id')->toArray();
              $data[] = array("Total unique non-effective calls:-", (string)count($noneffCalls));
              $parties_added = $parties->where('created_by', $salesman_id )->where('party_added', $salesman_order_date)->pluck('id')->toArray();
              $parties_added_on_date = $parties->where('created_by', $salesman_id )->where('party_added', $salesman_order_date)->pluck('id')->toArray(); 
              $data[] = array("New Parties Added:- ", (string)count($parties_added));
              $data[] = $cat;
              $data[] = $prod;
              $data[] = $prod_variants;
              $data[] = array();
              $pd_total = array();
              $tot_eff_calls = 0;
              $tot_non_eff_calls = 0;
              foreach($parties as $party){
                $partyOrdersCount = $salesmanOrders->where('order_date', $salesman_order_date)->where('client_id', $party->id)->count();
                $partyNoOdersCount = $salesmanNoOrders->where('date', $salesman_order_date)->where('client_id', $party->id)->count();                                   
                if($partyOrdersCount>0 || ($partyNoOdersCount>0 || in_array($party->id,$parties_added_on_date))){
                  $party_name = trim($party->company_name);
                  $party_address = trim($party->address_1);
                  if(in_array($party->id, $parties_added_on_date)){
                    $added_this_date = "Yes";
                  }else{
                    $added_this_date = "No";
                  }
                  if(in_array($party->id, $effCalls)){
                    $status = "Ordered";
                  }else{
                    $status = $added_this_date=="Yes"?"None":"Not Ordered";
                  }
                  $order_details = $salesmanOrders->where('order_date', $salesman_order_date)->where('client_id', $party->id);
                  
                  if($order_details->count()>0){
                    $party_detail = array($party_name, $party_address, $status, $added_this_date);
                    $order_product_details = Order::where('orders.order_date', $salesman_order_date)
                              ->rightJoin('orderproducts', 'orders.id', 'orderproducts.order_id')
                              ->where('orders.client_id', $party->id)
                              ->whereIn('orders.delivery_status_id', $order_statuses)->get();
    
                    $indexCounter = 0;
                    foreach($companyProducts as $companyProduct){
                      $orderQty = 0;
                      if(isset($companyProduct->variant_id)){
                        $orderQty = $order_product_details->where('product_id', $companyProduct->product_id)->where('product_variant_id', $companyProduct->variant_id)->where('unit', $companyProduct->variant_unit)->sum('quantity');
                      }else{
                        $orderQty = $order_product_details->where('product_id', $companyProduct->product_id)->where('unit', $companyProduct->product_unit)->sum('quantity');
                      }
                      if(array_key_exists($indexCounter, $grandTotal)) $grandTotal[$indexCounter] = strval((int)$grandTotal[$indexCounter]+$orderQty);
                      else  $grandTotal[$indexCounter] = "$orderQty";
                      ++$indexCounter;
                      array_push($party_detail, "$orderQty");
                    }
                  }elseif(!$order_details->first()||in_array($party->id,$parties_added_on_date)||$partyNoOdersCount>0){
                    $party_detail = array($party_name, $party_address, $status, $added_this_date);
                    foreach($companyProducts as $companyProduct){
                      array_push($party_detail, "-");
                    }
                  }
                  $data[] = $party_detail;
                  $flag = true;
                }
              }
              $data[] = array();
              $data[] = array_merge(array("Grand Total:- ", "", "", ""), $grandTotal);
              if($flag){
                if($this->getCompanySettings($company_id)['ncal']==0){
                  $dat_array[$salesman_order_date] = $data;
                }else{
                  $dat_array[getDeltaDateForReport($salesman_order_date)] = $data;
                }
              }
              unset($data);
            }
          }else{
            $filename_download = 'SalesReport_'.$salesman->name.'(Agg)_'.$date;
            $after_download_name = 'SalesReport_'.$salesman->name.'(Agg)_'.$date.'_'.time();
            $data[] = array($date);
            $data[] = array();
            $effCalls = $salesmanOrders->unique('client_id')->pluck('client_id')->toArray();
            $data[] = array("Parties who have placed order:-", strval(count($effCalls)));
            $data["non_eff_calls"] = array();
            if($startDate==$endDate){
              $parties_added = $parties->where('created_by', $salesman_id )->where('party_added', $startDate)->pluck('id')->toArray();
              $parties_added_on_date = $parties->where('created_by', $salesman_id )->where('party_added', $startDate)->pluck('id')->toArray(); 
              
            }else{
              $parties_added = $parties->where('created_by', $salesman_id )->where('party_added', '>=', $startDate)->where('party_added', '<=', $endDate)->pluck('id')->toArray();
              $parties_added_on_date = $parties->where('created_by', $salesman_id )->where('party_added', '>=', $startDate)->where('party_added', '<=', $endDate)->pluck('id')->toArray(); 
            }
            $data[] = array("New Parties Added:- ", count($parties_added));
            $data[] = $cat;
            $data[] = $prod;
            $data[] = $prod_variants;
            $data[] = array();
            $total_array = array("Grand Total:-", "", "");
            $grandTotal = array("", "", "");
            $flag = false;
            $eff_calls_total = 0;
            $non_eff_calls_total = 0; 
            $parties_placed_no_order = 0;
            foreach($parties as $party){
              $party_name = trim($party->company_name);
              $party_address = trim($party->address_1);
              $effective_calls = 0;
              $effective_calls = $salesmanOrders->where('client_id', $party->id)->unique('order_id')->count();
              $eff_calls_total += $effective_calls;
              $non_effective_calls = 0;
              $non_effective_calls = $salesmanNoOrders->where('client_id', $party->id)->count();
              $non_eff_calls_total += $non_effective_calls;
      
              $order_details = $salesmanOrders->where('client_id', $party->id);
              // $order_product_details = $order_details;
              $order_product_details = Order::whereBetween('orders.order_date', [$startDate, $endDate])
                              ->rightJoin('orderproducts', 'orders.id', 'orderproducts.order_id')
                              ->where('orders.client_id', $party->id)
                              ->whereIn('orders.delivery_status_id', $order_statuses)->get();
              if(in_array($party->id, $parties_added_on_date)){
                $added_between_dates = "Yes";
              }else{
                $added_between_dates = "No";
              }
                  
              if($order_details->count()>0){
                $indexCounter = 0;
                $party_detail = array($party_name, $party_address,$added_between_dates, "$effective_calls", "$non_effective_calls");
                foreach($companyProducts as $companyProduct){
                  $orderQty = 0;
                  if(isset($companyProduct->variant_id)){
                    $orderQty = $order_product_details->where('product_id', $companyProduct->product_id)->where('product_variant_id', $companyProduct->variant_id)->where('unit', $companyProduct->variant_unit)->sum('quantity');
                  }else{
                    $orderQty = $order_product_details->where('product_id', $companyProduct->product_id)->where('unit', $companyProduct->product_unit)->sum('quantity');
                  }
                  if(array_key_exists($indexCounter, $grandTotal)) $grandTotal[$indexCounter] = strval((int)$grandTotal[$indexCounter]+$orderQty);
                  else  $grandTotal[$indexCounter] = "$orderQty";
                  ++$indexCounter;
                  array_push($party_detail, "$orderQty");
                }
                $data[] = $party_detail;
              }elseif($non_effective_calls>0||in_array($party->id,$parties_added_on_date)){
                $parties_placed_no_order += 1;
                $party_detail = array($party_name, $party_address,$added_between_dates, "$effective_calls", "$non_effective_calls");
                foreach($companyProducts as $companyProduct){
                  array_push($party_detail, "-");
                }
                $data[] = $party_detail;
              }
              $flag = true;
              unset($party_detail);
            }
            $data["non_eff_calls"] = array("Parties who have not placed order:-", "$parties_placed_no_order");
            array_push($total_array, "$eff_calls_total");
            array_push($total_array, "$non_eff_calls_total");
            $data[] = array();
            $data[] = array_merge($total_array, $grandTotal);
            if($flag == true){
              $dat_array[$date] = $data;
            }
            unset($eff_calls_total);
            unset($non_eff_calls_total);
            unset($total_array);
          }
          Excel::store(new MultiSheetExport($dat_array), $after_download_name.'.'.$type, 'dailySalesmanReports');
      
          unset($dat_array);
          $processing->filename = $filename_download.'.'.$type;
          $processing->download_link = env('APP_URL').'storage/excel/reports/dailySalesmanReports/' . $after_download_name . '.' . $type;
          $processing->processing = 2;
          $processing->update();
        }catch(\Exception $e){
          Log::error(
            print_r(array(
              "Salesman Party-wise Report", 
              $e->getMessage()), 
              true)
          );
          $processing->processing = 0;
          $processing->save();
        }
      }
    }
  }
    
}