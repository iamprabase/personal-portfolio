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
use App\ProductReturn;
use App\GenerateReport;
use App\ProductVariant;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartyWiseReturn extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'party-wise-return:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel party-wise-return report(Party-wise Returns Report)';

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
      $processing = GenerateReport::whereReportType('Return Report')
                ->whereNULL('download_link')
                ->whereNULL('processing')
                ->first();
      if($processing){

        $company_id = $processing->company_id;
        $type = 'xlsx';
        $client_id = json_decode($processing->party_id);
        $generated_by = $processing->generated_by;
        $startDate = $processing->startDate; 
        $endDate = $processing->endDate;
        $report_type = $processing->report_type;
        $report_cat = $processing->report_cat;
        try{
          if($startDate==$endDate){
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate;
            }else{
              $date = getDeltaDateForReport($startDate);
            }
          }else{
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate.'-'.$endDate;
            }else{
              $date = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
            }
          }
          $employee = Employee::find($generated_by);
          $handled_employees = $employee->user->handleQuery('employee')->pluck('id')->toArray();
  
          $prepQuery = ProductReturn::where('returns.company_id', $company_id)
                            ->whereIn('returns.employee_id', $handled_employees)
                            ->whereIn('returns.client_id', $client_id)
                            ->whereBetween('returns.return_date', [$startDate, $endDate]);
  
          $returnCount = (clone $prepQuery);
          $return_exists = $returnCount->count();
            
  
          $client = $employee->user->handleQuery('client')->select("clients.id as client_id", "clients.company_name as name", "clients.client_type")
                      ->whereIn('clients.id', $client_id)
                      ->orderby('clients.company_name', 'asc')
                      ->get();
          $party_types = PartyType::select('partytypes.id', 'partytypes.name')
                          ->where('company_id', $company_id)
                          ->get();
  
          $client->map(function ($item) use ($party_types) {
            if(isset($item->client_type)){
              try{
                $item['client_type_name'] = $party_types->filter(function ($party_item, $party_key) use ($item) {
                return $party_item->id==$item->client_type;
                })->first()->name;
              }catch(\Exception $e){
                $item['client_type_name'] = "";
              }
            }else{
              $item['client_type_name'] = "Unspecified";
            }
          });
  
          $getReturns = (clone $prepQuery);
          $returnsAdded = $getReturns->join('return_details', 'return_details.return_id', 'returns.id')
                            ->get();
            
          $products = Product::where('company_id', $company_id)
                      ->orderby('category_id', 'DESC')
                      ->get();
  
          $brands = Brand::select('brands.id', 'brands.name')
                      ->where('company_id', $company_id)
                      ->orderby('name', 'ASC')
                      ->get()->toArray();
          $unspecified_brand["id"] = 0;
          $unspecified_brand["name"] = "Unspecified";
          array_push($brands, $unspecified_brand);
  
          $categories = Category::select('categories.id', 'categories.name')
                          ->where('company_id', $company_id)
                          ->orderby('name', 'ASC')
                          ->get()->toArray();
          $unspecified_category["id"] = 0;
          $unspecified_category["name"] = "Unspecified";
          array_push($categories, $unspecified_category);
  
          if(count($client_id)==1){
            $name = str_replace(" ","",getClient($client_id)->company_name);
          }else{
            $name = "Multiple_Party";
          }
          $time = time();
          $type = 'xlsx';
          if($report_cat == "By Brand"){
            $filename_download = $name."Returns_Reports_By_Brand_".$date;
            $data_sheet = $this->getBrandExcelReport($brands, $products, $client, $returnsAdded, $repKey="returns");
  
            $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');
  
  
          }else if($report_cat == "By Category"){
            $filename_download = $name."Returns_Reports_By_Category_".$date;
            $data_sheet = $this->getCategoryExcelReport($categories, $products, $client, $returnsAdded, $repKey="returns");
  
            $_excel = Excel::store(new MultiSheetExport($data_sheet), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');
  
  
          }else if($report_cat == "Consolidated"){
            $filename_download = $name."Returns_Brand_Reports_Cosnolidated_".$date;
            $data_sheet = $this->getConsolidatedExcelReport($products, $client, $returnsAdded, $repKey="returns");
  
            $exportData['Consolidated'] = $data_sheet;
            $_excel = Excel::store(new MultiSheetExport($exportData), urlencode($filename_download.$time).'.'.$type, 'prodReturnReports');
  
          } 
          $processing->filename = $filename_download;
          $processing->download_link = env('APP_URL').'storage/excel/reports/prodReturnReports/'.urlencode($filename_download.$time).'.'.$type;
          $processing->processing = 2;
          $processing->update();
        }catch(\Exception $e){
          Log::error(
            print_r(array(
              "Party-wise Return Report", 
              $e->getMessage()), 
              true)
          );
          $processing->processing = 0;
          $processing->save();
        }
        
      }
    }
  }

  private function getVariants($product_id){
    $variants = Product::join('product_variants','product_variants.product_id','products.id')
                ->where('products.id', $product_id)
                ->get();

    return $variants;
  }

  private function getHeaderArray($brandOrCatProd, $search_by){
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

  private function getColTotal($init_arr, $prods, $prod_total){
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

  private function getBrandExcelReport($brands, $products, $client, $stocks_taken, $report_key){
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

  private function getCategoryExcelReport($categories, $products, $client, $stocks_taken, $report_key){
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

  private function getConsolidatedExcelReport($products, $client, $stocks_taken, $report_key){
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

  
    
}