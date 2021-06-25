<?php 


namespace App\Services;

use App\Order;
use App\Client;
use App\Product;
use App\UnitTypes;
use App\ClientSetting;
use App\ProductVariant;
use App\ModuleAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductOrderDetailReportService{

  private $companyId;
  private $startDate;
  private $endDate;
  private $orderStatus;
  private $employees;
  private $parties;
  private $groupBy;
  private $include_dispatch_detail;

  public function __construct($companyId, $startDate, $endDate, $orderStatus, $employees, $parties, $groupBy, $include_dispatch_detail){
    $this->companyId = $companyId;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
    $this->orderStatus = $orderStatus ? json_decode($orderStatus) : $orderStatus;
    $this->employees = $employees ? json_decode($employees) : $employees;
    $this->parties = $parties ? json_decode($parties) : $parties;
    $this->groupBy = $groupBy ? $groupBy : $groupBy;
    $this->include_dispatch_detail = $include_dispatch_detail? $include_dispatch_detail : $include_dispatch_detail;
  }

  private function getCompanySettings($company_id){
    try{
      $setting = ClientSetting::whereCompanyId($company_id)->first();
      
      return $setting;
    }catch(\Exception $e){
      Log::error($e->getMessage());
      return 0;
    }
  }

  public function getOrders(){
    $setting = $this->getCompanySettings($this->companyId);
    $currencySymbol = $setting->currency_symbol;
    $orderPrefix = $setting->order_prefix;
    $ncal = $setting->ncal;
    $orderStatuses = ModuleAttribute::whereCompanyId($this->companyId)->pluck('title', 'id')->toArray();
    $orders = Order::where('orders.company_id', $this->companyId)
              ->whereBetween('orders.order_date', [$this->startDate, $this->endDate] )
              ->where(function($filterQuery) {
                if($this->orderStatus){
                  $filterQuery->whereIn('orders.delivery_status_id', $this->orderStatus);
                }
                if($this->employees){
                  $filterQuery->whereIn('orders.employee_id', $this->employees);
                }
                if($this->parties){
                  $filterQuery->whereIn('orders.client_id', $this->parties);
                }
              })
              ->leftJoin('orderproducts', 'orderproducts.order_id', 'orders.id')
              ->whereNull('orderproducts.deleted_at')
              ->orderBy('orders.id', 'desc')
              ->groupBy($this->groupBy)
              ->get(['orders.order_no',
                'orders.client_id',
                'orders.order_date',
                'orders.delivery_status_id',
                'orders.delivery_date',
                'orders.delivery_place',
                'orders.transport_name',
                'orders.transport_number',
                'orders.billty_number',
                'orders.delivery_note',
                'orderproducts.product_id',
                'orderproducts.product_variant_id',
                'orderproducts.unit',
                DB::raw('SUM(orderproducts.quantity) as total_quantity'),
                DB::raw('SUM(orderproducts.amount) as total_amount'),
                DB::raw('SUM(orderproducts.pdiscount) as discount'),
                'orderproducts.pdiscount_type',
                DB::raw('SUM(orderproducts.rate) as total_rate'),
              ])
              ->map(function($order) use($orderPrefix, $ncal, $currencySymbol, $orderStatuses) {
                if($ncal==1){
                  $order->order_date = getDeltaDateForReport( $order->order_date );
                  $order->delivery_date = $order->delivery_date ? getDeltaDateForReport( $order->delivery_date ) : null;
                }
                $order->order_no = $orderPrefix.$order->order_no;
                $order->delivery_status = $orderStatuses[$order->delivery_status_id];
                
                $order->total_rate = "$order->total_rate";
                $order->discount = $order->discount ? $order->discount : "0"; 
                $order->discount_and_type = $order->pdiscount_type == "%" ? $order->discount." %" : $currencySymbol." ".$order->discount;
                $order->total_amount = "$order->total_amount";
                $order->total_quantity = "$order->total_quantity";
                return $order;
              });
    return $orders;
  }

  public function mapWithProductNames($orders){
    $productIds = $orders->unique('product_id')->pluck('product_id');
    $variantIds = $orders->where('product_variant_id', '!=', NULL)->unique('product_variant_id')->pluck('product_variant_id');
    $products = Product::whereIn('id', $productIds)->get(['product_name as name', 'product_code', 'id']);
    $variants = ProductVariant::whereIn('id', $variantIds)->pluck('variant', 'id')->toArray();
    $unit = $orders->unique('unit')->pluck('unit');
    $units = UnitTypes::whereIn('id', $unit)->pluck('symbol','id');

    $mappedData = $orders->map(function($order) use($products, $variants, $units){
      $product = $products->where('id', $order->product_id)->first();
      if($product) {
        $order->product_name = $product->name;
        $order->product_code = $product->product_code;
        $order->unit_symbol = $units[$order->unit];
        $order->variant_name = "";
        if($order->product_variant_id){
          $order->variant_name = $variants[$order->product_variant_id];
        }
      }
      return $order;
    });

    return $mappedData;
  }

  public function mapWithParties($orders){
    $clients = Client::whereIn('id', $this->parties)->get(['company_name as name', 'id', 'client_code']);

    $mappedData = $orders->map(function($order) use($clients){
      $client = $clients->where('id', $order->client_id)->first();
      if($client) {
        $order->client_company_name = $client->name;
        $order->client_code = $client->client_code;
      }else{
        $order->client_company_name = NULL;
        $order->client_code = NULL;
      }
      return $order;
    });

    return $mappedData;
  }

  public function getMappedData ($finalResults) {
    $setting = $this->getCompanySettings($this->companyId);
    
    if($this->startDate == $this->endDate){
      $sheetName = $this->startDate;
      if($setting->ncal==1){
        $sheetName = getDeltaDateForReport($this->startDate);
      }
    }else{
      $sheetName = $this->startDate . 'to' . $this->endDate;
      if($setting->ncal==1){
        $sheetName = getDeltaDateForReport($this->startDate).'_to_'.getDeltaDateForReport($this->endDate);
      }
    }

    $fileHeaders = array(
      0 => "Order No.", 
      1 => "Order Date", 
      2 => "Party Code", 
      3 => "Party Name", 
      4 => "Product Code", 
      5 => "Product Name", 
      6 => "Variant Name", 
      7 => "Unit",
      8 => "Total Quantity",
      9 => "Applied Rate",
      10 => "Discount",                      
      11 => "Total Amount",
      12 => "Status",
      13 => "Dispatch Date",
      14 => "Dispatch Place",
      15 => "Transport Name",
      16 => "Transport Number",
      17 => "Billty Number",
      18 => "Dispatch Note",
    );
    
    $property = array(
      0 => 'order_no', 
      1 => 'order_date', 
      2 => 'client_code', 
      3 => 'client_company_name', 
      4 => 'product_code', 
      5 => 'product_name', 
      6 => 'variant_name', 
      7 => 'unit_symbol', 
      8 => 'total_quantity', 
      9 => 'total_rate', 
      10 => 'discount_and_type', 
      11 => 'total_amount', 
      12 => 'delivery_status', 
      13 => 'delivery_date', 
      14 => 'delivery_place', 
      15 => 'transport_name', 
      16 => 'transport_number', 
      17 => 'billty_number', 
      18 => 'delivery_note'
    );
    
    if($setting->product_level_discount == 0 || $setting->order_with_amt == 1){
      unset($fileHeaders[10]);
      unset($property[10]);
      if($setting->order_with_amt == 1){
        unset($property[9]);
        unset($property[11]);
        unset($fileHeaders[9]);
        unset($fileHeaders[11]);
      }
    }

    if($this->include_dispatch_detail == 0){
      unset($property[13]);
      unset($property[14]);
      unset($property[15]);
      unset($property[16]);
      unset($property[17]);
      unset($property[18]);
      unset($fileHeaders[13]);
      unset($fileHeaders[14]);
      unset($fileHeaders[15]);
      unset($fileHeaders[16]);
      unset($fileHeaders[17]);
      unset($fileHeaders[18]);
    }

    $exportData = array(
                    $sheetName => array($fileHeaders)
                  );

    foreach($finalResults as $result) {
      $data = array();
      foreach($property as $prop) {
        $data[] = $result->$prop;
      }
      
      array_push($exportData[$sheetName], $data );
    }

    return $exportData;
  }

}