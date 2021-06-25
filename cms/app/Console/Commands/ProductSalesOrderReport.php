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

class ProductSalesOrderReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'product-sales:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel product-sales report(Product Sales Order Report)';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  private function getCompanySettings($company_id){
    try{
      $setting = ClientSetting::whereCompanyId($company_id)->first()->toArray();
      
      return $setting;
    }catch(\Exception $e){
      Log::error($e->getMessage());
      return 0;
    }
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $processing_stock_reports_count = GenerateReport::where('processing','1')->count();
    
    if($processing_stock_reports_count<3){
      $processing = GenerateReport::whereReportType('ProductSales')
                ->whereNULL('download_link')
                ->whereNULL('processing')
                ->first();
      if($processing){
        try{
          $reportid = $processing->id;
          $processing->processing = 1;
          $processing->save();
          $company_id = $processing->company_id;
          $report_type = $processing->report_cat=='daily'?30:12;
          $report_cat = $processing->report_cat;
          $generated_by = $processing->generated_by;
          $type = 'xlsx';
          $today = date('Y-m-d');
          $salesman_ids = empty($processing->employee_id)?[]:json_decode($processing->employee_id);
          $party_ids = empty($processing->party_id)?[]:json_decode($processing->party_id);
          $selected_statuses = json_decode($processing->order_status);
          $startDate = $processing->start_date;
          $endDate = $processing->end_date;
          $dates = $this->getDatesFromRange($startDate, $endDate);
          if($startDate == $endDate){
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date_head = getDeltaDateForReport($startDate);  
              $file_date = getDeltaDateForReport($startDate);
            }else{
              $date_head = getDeltaDateForReport($startDate);
              $file_date = getDeltaDateForReport($startDate);
            }
          }else{
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date_head = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);  
              $file_date = getDeltaDateForReport($startDate).' to '.getDeltaDateForReport($endDate);
            }else{
              $date_head = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
              $file_date = getDeltaDateForReport($startDate).' to '.getDeltaDateForReport($endDate);
            }
          }

          $orders = Order::join('orderproducts', 'orderproducts.order_id', 'orders.id')
                  ->where('orders.company_id', $company_id)
                  ->whereBetween('orders.order_date', [$startDate, $endDate])
                  ->whereIn('orders.delivery_status_id', $selected_statuses)
                  ->where(function($query) use($salesman_ids, $party_ids){
                    $query->whereIn('orders.employee_id', $salesman_ids);
                    $query->whereIn('orders.client_id', $party_ids);                                
                  })->whereNull('orders.deleted_at')
                  ->get();
          $brands = Brand::where('company_id', $company_id)
                      ->orderby('name', 'ASC')
                      ->get(['id', 'name']);
          $categories = Category::where('company_id', $company_id)
                        ->orderby('name', 'ASC')
                        ->get(['id', 'name']);
          $units = DB::table('unit_types')->where('company_id', $company_id)
                    ->orderby('name', 'ASC')
                    ->get(['id', 'symbol']);
          
          $companyProductVariants = Product::where('products.company_id', $company_id)
                                      ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
                                      ->orderBy('products.category_id', 'desc')
                                      ->orderBy('products.id', 'desc')
                                      ->get(['products.id as product_id','products.product_name', 'products.brand','products.category_id','products.unit as product_unit', 'products.status as product_status', 'products.variant_flag', 'product_variants.id as variant_id', 'product_variants.variant', 'product_variants.unit as variant_unit']);
          $newCollec = collect();
          $prevObj = null;
          foreach($companyProductVariants as $product){
            if($product->product_status=="Inactive"){
              $checkInactiveAndOrdered = (clone $orders)->where('product_id', $product->product_id)->count();
              if($checkInactiveAndOrdered==0) continue;
            }
            if(isset($product->variant_id)){
              if(isset($prevObj)){
                if($prevObj->product_id!=$product->product_id){
                  $checkIfOrderExistBeforeVariant = array_unique((clone $orders)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->pluck('unit')->toArray());
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
              $getOrderDetails = array_unique((clone $orders)->where('product_id', $product->product_id)->where('product_variant_id', $product->variant_id)->where('unit','!=', NULL)->pluck('unit')->toArray());
              if(count($getOrderDetails)>0){
                foreach($getOrderDetails as $orderUnit){
                  if($orderUnit!=$product->variant_unit && isset($orderUnit)){
                    $cloned = (clone $product);
                    $cloned->variant_unit = $orderUnit;
                    $newCollec->push($cloned);
                  }
                }
              }
            }else{
              $getOrderDetails = array_unique((clone $orders)->where('product_id', $product->product_id)->where('unit', '!=', NULL)->pluck('unit')->toArray());
              if(count($getOrderDetails)>0){
                foreach($getOrderDetails as $orderUnit){
                  if($orderUnit!=$product->product_unit && isset($orderUnit)){
                    $cloned = (clone $product);
                    $cloned->product_unit = $orderUnit;
                    $newCollec->push($cloned);
                  }
                }
              }
            }
            unset($prevObj);
            $prevObj = $product;
            $newCollec->push($product);
          }

          unset($companyProductVariants);
          $companyProductVariants = $newCollec;
          $companyProducts = $companyProductVariants
                              ->map(function($item) use($categories, $brands, $units){
                                $item['brand_name'] = "Unspecified";
                                $item['category_name'] = "Unspecified";
                                $item['unit_symbol'] = null;
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
                              })->sortBy('product_name');
          unset($companyProductVariants);

          $data = array();                    
          $prod_header = array("Product Name","Variant","Category","Brand","Unit","Total (in selected period)");
          $grand_total = array("Grand-Total", "", "", "", "", 0);
          $getCalendarSetting = $this->getCompanySettings($company_id)['ncal'];
          $headerFlag = true;
          if($report_type==30){
            $grandTotal = 0;
            foreach($companyProducts as $product){
              $reportData = array();
              $reportData[] = $product->product_name;
              $reportData[] = $product->variant;
              $reportData[] = $product->category_name;
              $reportData[] = $product->brand_name;
              $reportData[] = $product->unit_symbol;
              $reportData[5] = "";

              $total_in_sel_period = 0;
        
              foreach($dates as $date){
                if(isset($product->variant_id))
                  $getSumOfOrders = $this->getQuantitySum($orders, $date, $product->product_id, $product->variant_id, $product->variant_unit);
                else 
                  $getSumOfOrders = $this->getQuantitySum($orders, $date, $product->product_id, $product->variant_id, $product->product_unit);
                if($headerFlag){
                  if($getCalendarSetting==0){
                    $formatted_date = date("jS F Y", strtotime($date));  
                  }else{
                    $formatted_date = getDeltaDateForReport($date);
                  }
                  array_push($prod_header,$formatted_date);
                }
                $total_in_sel_period += $getSumOfOrders;
                
                if(!isset($grand_total[$date])) $grand_total[$date] = strval($getSumOfOrders);
                elseif(isset($grand_total[$date])) $grand_total[$date] = strval((int)$grand_total[$date]+$getSumOfOrders);

                $grandTotal += $getSumOfOrders;
                
                array_push($reportData, "$getSumOfOrders");
                unset($getSumOfOrders);
              }

              
              if ($headerFlag) {
                $headerFlag = false;
                $data[] = $prod_header;
              }
              $reportData[5] = "$total_in_sel_period";
              $data[] = $reportData; 
            }
            $grand_total[5] = "$grandTotal";
            $data[] = $grand_total;
          }elseif($report_type==12){
            $grandTotal = 0;
            $loopDates = array();
            foreach($dates as $date){
              if($getCalendarSetting==0){
                $formatted_date = date("F Y", strtotime($date));
              }else{
                $formatted_date = getMthYrDeltaDate($date);
              }
              if(!in_array($formatted_date, $prod_header)){
                array_push($prod_header,$formatted_date);
              }
              $month = date("n", strtotime($date));
              $year = date("Y", strtotime($date));
              $loopDates[$month.'_'.$year][] = $date;
            }
            foreach($companyProducts as $product){
              $reportData = array();
              $reportData[] = $product->product_name;
              $reportData[] = $product->variant;
              $reportData[] = $product->category_name;
              $reportData[] = $product->brand_name;
              $reportData[] = $product->unit_symbol;
              $reportData[5] = "";

              $total_in_sel_period = 0;
        
              foreach($loopDates as $key=>$dates){
                if(isset($product->variant_id))
                  $getSumOfOrders = $this->getMontlyQuantitySum((clone $orders), $dates, $product->product_id, $product->variant_id, $product->variant_unit);
                else
                  $getSumOfOrders = $this->getMontlyQuantitySum((clone $orders), $dates, $product->product_id, $product->variant_id, $product->product_unit);

                $total_in_sel_period += $getSumOfOrders;
                
                if(!isset($grand_total[$key])) $grand_total[$key] = strval($getSumOfOrders);
                elseif(isset($grand_total[$key])) $grand_total[$key] = strval((int)$grand_total[$key]+$getSumOfOrders);

                $grandTotal += $getSumOfOrders;
                
                array_push($reportData, "$getSumOfOrders");
                unset($getSumOfOrders);
              }

              
              if ($headerFlag) {
                $headerFlag = false;
                $data[] = $prod_header;
              }
              $reportData[5] = "$total_in_sel_period";
              $data[] = $reportData; 
            }
            $grand_total[5] = "$grandTotal";
            $data[] = $grand_total;    
          }

          
          if($report_type==30){
            $sheet_title = "Daily_".$date_head;
            $filename_download = 'ProductSalesReport_'.$file_date.'(dly)'.'.xlsx';
            $filename = 'ProductSalesReport_'.$file_date.'(dly)_'.time();
            $report_type_name = "daily";
          }elseif($report_type==12){
            $sheet_title = "Monthly_".$date_head;
            $filename_download = 'ProductSalesReport_'.$file_date.'(mthly)'.'.xlsx';
            $filename = 'ProductSalesReport_'.$file_date.'(mthly)_'.time();
            $report_type_name = "monthly";
          }
          $information[] = array();
          $information[] = array("Parties", "","Salesmen");
          if($salesman_ids!= NULL && $party_ids!=NULL){
            if(count($party_ids)>count($salesman_ids)){
              $len = count($party_ids);
              $employee_names = Employee::whereIn('id', $salesman_ids)->pluck('name')->toArray();
              $client_company_names = Client::whereIn('id', $party_ids)->pluck('company_name')->toArray();
            
              // for($ind=0; $ind<$len; $ind++){
              //   $party_salsman = array();
              //   $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
              //   if($ind<count($salesman_ids)){
              //     $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
              //   }else{
              //     $salsman_name = NULL;
              //   }
              //   array_push($party_salsman,$prty_name, "", $salsman_name);
              //   unset($prty_name);
              //   unset($salsman_name);
              //   $information[] = $party_salsman;
              //   unset($party_salsman);
              // }
              for($ind=0; $ind<$len; $ind++){
                $party_salsman = array();
                $prty_name = array_key_exists($ind, $client_company_names)?$client_company_names[$ind] : NULL;
                if($ind<count($salesman_ids)){
                  $salsman_name = array_key_exists($ind, $employee_names)?$employee_names[$ind] : NULL;
                }else{
                  $salsman_name = NULL;
                }
                array_push($party_salsman,$prty_name, "", $salsman_name);
                unset($prty_name);
                unset($salsman_name);
                $information[] = $party_salsman;
                unset($party_salsman);
              }
            }elseif(count($party_ids)<=count($salesman_ids)){
              $len = count($salesman_ids);
              $employee_names = Employee::whereIn('id', $salesman_ids)->pluck('name')->toArray();
              $client_company_names = Client::whereIn('id', $party_ids)->pluck('company_name')->toArray();
            
              // for($ind=0; $ind<$len; $ind++){
              //   $party_salsman = array();
              //   $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
              //   if($ind<count($party_ids)){
              //       $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
              //   }else{
              //       $prty_name = NULL;
              //   }
              //   array_push($party_salsman,$prty_name, "", $salsman_name);
              //   unset($prty_name);
              //   unset($salsman_name);
              //   $information[] = $party_salsman;
              //   unset($party_salsman);
              // }
              for($ind=0; $ind<$len; $ind++){
                $party_salsman = array();
                $salsman_name = array_key_exists($ind, $employee_names)?$employee_names[$ind] : NULL;
                if($ind<count($party_ids)){
                  $prty_name = array_key_exists($ind, $client_company_names)?$client_company_names[$ind] : NULL;
                }else{
                  $prty_name = NULL;
                }
                array_push($party_salsman,$prty_name, "", $salsman_name);
                unset($prty_name);
                unset($salsman_name);
                $information[] = $party_salsman;
                unset($party_salsman);
              }
            }
          }elseif($salesman_ids!= NULL && $party_ids==NULL){
            $len = count($salesman_ids);
            // for($ind=0; $ind<$len; $ind++){
            //   $party_salsman = array();
            //   $prty_name = NULL;
            //   $salsman_name = (getEmployee($salesman_ids[$ind])->name)?getEmployee($salesman_ids[$ind])->name:NULL;
            //   array_push($party_salsman,$prty_name, "", $salsman_name);
            //   unset($prty_name);
            //   unset($salsman_name);
            //   $information[] = $party_salsman;
            //   unset($party_salsman);
            // }
            $employee_names = Employee::whereIn('id', $salesman_ids)->pluck('name')->toArray();
            foreach($employee_names as $employee_name){
              $party_salsman = array();
              $prty_name = NULL;
              $salsman_name = $employee_name;
              array_push($party_salsman,$prty_name, "", $salsman_name);
              unset($prty_name);
              unset($salsman_name);
              $information[] = $party_salsman;
              unset($party_salsman);
            }
          }elseif($salesman_ids== NULL && $party_ids!=NULL){
            $len = count($party_ids);
            // for($ind=0; $ind<$len; $ind++){
            //   $party_salsman = array();
            //   $prty_name = (getClient($party_ids[$ind])->company_name)?getClient($party_ids[$ind])->company_name:NULL;
            //   $salsman_name = NULL;
            //   array_push($party_salsman,$prty_name, "", $salsman_name);
            //   unset($prty_name);
            //   unset($salsman_name);
            //   $information[] = $party_salsman;
            //   unset($party_salsman);
            // }
            $client_company_names = Client::whereIn('id', $party_ids)->pluck('company_name')->toArray();
            foreach($client_company_names as $client_company_name){
              $party_salsman = array();
              $prty_name = $client_company_name;
              $salsman_name = NULL;
              array_push($party_salsman,$prty_name, "", $salsman_name);
              unset($prty_name);
              unset($salsman_name);
              $information[] = $party_salsman;
              unset($party_salsman);
            }
          }else{
            $party_salsman = array();
            $prty_name = "None";
            $salsman_name = "None";
            array_push($party_salsman,$prty_name, "", $salsman_name);
            unset($prty_name);
            unset($salsman_name);
            $information[] = $party_salsman;
            unset($party_salsman);
          }
          $excelExport["Information Sheet"] = $information;
          $excelExport[$sheet_title] = $data;
          
          $store = Excel::store(new MultiSheetExport($excelExport), $filename.'.'.$type, 'productSalesReports');

          $processing->filename = $filename_download;
          $processing->download_link = env('APP_URL').'storage/excel/reports/productSalesReports/' . $filename . '.' . $type;
          $processing->processing = 2;
          $processing->update();
        }catch(\Exception $e){
          Log::error(
            print_r(array(
              "Product Sales Report", 
              $e->getMessage()), 
              true)
          );
          $processing->processing = 0;
          $processing->save();
        }
        
      }
    }
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

  private function getDatesFromRange($start, $end, $format='Y-m-d') {
    if($start == $end){
      return array($start);
    }else{
      return array_map(function($timestamp) use($format) {
          return date($format, $timestamp);
      },
      range(strtotime($start) + ($start < $end ? 4000 : 8000), strtotime($end) + ($start < $end ? 8000 : 4000), 86400));
    }
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

  
    
}