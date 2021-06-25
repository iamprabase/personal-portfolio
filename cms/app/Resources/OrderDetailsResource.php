<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $product = $this->product;
        if(isset($this->product_variant_id)){
          $variant = $this->product_variant;
          try{
            $visibility = $variant->app_visibility==1?true:false;
            $moq = $variant->moq;
            $allowed_moq = $this->quantity>=$moq?true:false;

          }catch(\Exception $e){
            $visibility = false;
            $moq = 1;
            $allowed_moq = false;
          }
        }else{
          $visibility = $product->app_visibility==1?true:false;
          $moq = $product->moq;
          $allowed_moq = $this->quantity>=$moq?true:false;
        }
        if($product->categories){
          $category_name = $product->categories->name;
        }else{
          $category_name = NULL;
        }
        if($product->image){
          $company_name = $product->companies->domain;
          $tempImagePath = "/storage/app/public/uploads/" . $company_name . '/products/' . $product->image;
          $image_path = 'https://'.$_SERVER['HTTP_HOST'].'/cms'.$tempImagePath;
        }else{
          $image_path = null;
        }

        $rate = $this->rate;
        $pdiscount = $this->pdiscount?$this->pdiscount:0;
        $pdiscount_type = $this->pdiscount_type;
        if($pdiscount_type=="oAmt"){
          $pdiscount = $pdiscount/$this->quantity;
          $rate = $rate - $pdiscount;
          $pdiscount_type = "Amt"; 
        }

        $data = [
                  'id' => $this->id,
                  'visibility' => $visibility,
                  'moq' => $moq,
                  'allowed_moq' => $allowed_moq,
                  'product_id' => $this->product_id,
                  'product_name' => $this->product_name,
                  'variant_id' => $this->product_variant_id,
                  'variant_name' => $this->product_variant_name,
                  'variant_colors' => $this->variant_colors,
                  'unit_id' => $this->unit,
                  'unit_name' => $this->unit_name,
                  'quantity' => $this->quantity,
                  'rate' => (string)round($rate,2),
                  'mrp' => $this->mrp,
                  'brand' => $this->brand,
                  'brand_name'=> $this->brands?$this->brands->name:null,
                  'category_name'=> $category_name,
                  'amount' => $this->amount,
                  'pdiscount' => (string)round($pdiscount, 2),
                  'pdiscount_type' => $pdiscount_type,
                  'short_desc' => $this->short_desc,  
                  'image' =>  $image_path, 
                ];
        if($this->order->product_level_tax_flag==1){
          $applied_taxes = $this->taxes()->withTrashed()->get();
          if(!empty($applied_taxes)) {
            $taxes = TaxResource::collection($applied_taxes);
            $data['applied_tax'] = $taxes;
          }else{
            $data['applied_tax'] = NULL;
          }
        }

        return $data;
    }
}
