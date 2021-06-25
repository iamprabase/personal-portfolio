<?php

namespace App\Http\Resources;

use App\Client;
use App\Product;
use App\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaxResource;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ClientOrderResource;
use App\Http\Resources\OutletSupplierResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ClientProductVariantResource;

class OutletClientResource extends JsonResource
{

    public function __construct($resource) {
      // Ensure we call the parent constructor
      parent::__construct($resource);
      $this->resource = $resource;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    { 
      $company = $this->companies;
      $company_settings = $company->clientsettings;
      $company_id = $company->id;
      $company_name = $company->company_name;
      $companies_taxes = $company->default_taxes;
      $order_fetch_route = route('api.outlet.order.fetch', [$this->id]);
      if($company_settings->accounting==0){
        $accounting_route =  null;
      }else{
        $accounting_route = route('api.outlet.accounts.fetch', [$this->id]);
      }
      $payment_route = route('api.outlet.payments.fetch', [$this->id]);
      
      $product_exists = $company->retailer_products;
      $products = $product_exists?ClientProductResource::collection($product_exists):null;
      $variant_exists = $company->retailer_product_variants;
      $variants = $variant_exists->first()?ClientProductVariantResource::collection($variant_exists):null;

      $rate_details = array();

      if($company->clientsettings->party_wise_rate_setup==1){
        $client_rates = $this->rates;
        if($client_rates){
          $rate_details = $client_rates->ratedetails;
        }
      }

      $data = array();

      if(!empty($products)){
        if($company->clientsettings->party_wise_rate_setup==1 && !empty($rate_details)){
          foreach($products as $product){
            $product_instance = $rate_details->where('product_id', $product->id)->where('variant_id', NULL);
            if($product_instance->first()){
              $product->mrp = $product_instance->first()->mrp; 
            }
            array_push($data, $product);
          }
        }else{
          foreach($products as $product){
            array_push($data, $product);
          }
        }
      }

      if(!empty($variants)){
        if($company->clientsettings->party_wise_rate_setup==1 && !empty($rate_details)){
          foreach($variants as $variant){
            $product_instance = Product::find($variant->product_id);
            if($product_instance->status=="Active"){
              $variant_instance = $rate_details->where('product_id', $variant->product_id)->where('variant_id', $variant->id);
              if($variant_instance->first()){
                $variant->mrp = $variant_instance->first()->mrp; 
              }
              array_push($data, $variant);
            }
          }
        }else{
          foreach($variants as $variant){
            $product_instance = Product::find($variant->product_id);
            if ($product_instance->status=="Active") {
              array_push($data, $variant);
            }
          }
        }
      }

      $supplier_name = $company_name;
      if($this->superior){
        $superior_instance = Client::withTrashed()->find($this->superior);
        if($superior_instance)  $supplier_name = $superior_instance->company_name." (".$supplier_name.")";
      }
      
      return [
        'id' => $company_id,
        'supplier_name' => $supplier_name,
        'client_id' => $this->id,
        'client_name' => $this->company_name,
        'credit_limit' => $this->credit_limit,
        'fetch_orders' => $order_fetch_route,
        'fetch_accounting_info' => $accounting_route,
        'fetch_payments' => $payment_route,
        'order_setting' => new OrderSettingResource($this->companies->clientsettings),
        'products' => empty($data)?null:$data,
        'orders' => ClientOrderResource::collection($this->orders),
        'taxes' => $companies_taxes->first()?TaxResource::collection($companies_taxes):NULL,
        'rates' => $rate_details
      ];
    }
}
