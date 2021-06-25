<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    { 
      $relted_setting = $this->client_outlet_settings;
      $order_with_qty_amt = $this->order_with_amt;
      $order_statuses = $this->company->order_statuses;
      if($relted_setting){
        $min_order_value = $relted_setting->min_order_value;
      }else{
        $min_order_value = null;
      }
      
      return [
        'currency_symbol' => $this->currency_symbol,
        'order_with_qty_amt' => $order_with_qty_amt,
        'product_level_tax' => $this->product_level_tax,
        'product_level_discount' => $this->product_level_discount,
        'min_order_value' => $min_order_value,
        'order_prefix' => $this->order_prefix,
        'custom_pricing' => $this->party_wise_rate_setup,
        'unit_conversion' => $this->unit_conversion,
      ];
    }
}
