<?php

namespace App\Http\Resources;

use App\Http\Resources\TaxResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\UnitConversionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {      
      $product_instance = $this->products;
      $variant_flag = $product_instance->variant_flag;
      $product_taxes = $product_instance->taxes;
      $brand_id = $product_instance->brand;
      $category_id = $product_instance->category_id;

      $brand_name = ($brand_id && $product_instance->brands)?$product_instance->brands->name:NULL;
      $category_name = ($category_id && $product_instance->categories)?$product_instance->categories->name:NULL;

      if($product_instance->image){
        $company_name = $product_instance->companies->domain;
        $tempImagePath = "/storage/app/public/uploads/" . $company_name . '/products/' . $product_instance->image;
      }else{
        $tempImagePath =  "/storage/app/public/uploads/" . "defaultprod.jpg";
      }
      $image_path = 'https://'.$_SERVER['HTTP_HOST'].'/cms'.$tempImagePath;
      if($variant_flag==1){
        return [
          'id' => md5($this->id*$product_instance->id),
          'product_id' => $product_instance->id,
          'product_name' => $product_instance->product_name,
          'variant_id' => $this->id,
          'variant_name' => $this->variant,
          'variant_attributes' => $this->variant_colors,
          'unit' => $this->unit,
          'unit_details' => new UnitResource($this->units),
          'mrp' => $this->mrp,
          'brand_id' => $brand_id,
          'brand_name' => $brand_name,
          'category_id' => $category_id,
          'category_name' => $category_name,
          'short_desc' => $this->short_desc,
          'pdiscount' => $this->discount?round($this->discount*$this->mrp/100, 2):null,
          'pdiscount_type' => 'Amt',
          'min_order_qty' => $this->moq,
          'max_order_qty' => $this->moq,
          'status' => $product_instance->status,
          'taxes' => $product_taxes->first()?TaxResource::collection($product_taxes):NULL,
          'image' =>  $image_path, 
          'unit_conversions' => $product_instance->conversions->count()>0?UnitConversionResource::collection($product_instance->conversions):NULL
        ];
      }
    }
}
