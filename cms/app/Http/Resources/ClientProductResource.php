<?php

namespace App\Http\Resources;

use App\Http\Resources\TaxResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\UnitConversionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {     
      $variant_flag = $this->variant_flag;
      $product_taxes = $this->taxes;
      $brand_id = $this->brand;
      $category_id = $this->category_id;

      $brand_name = ($brand_id && $this->brands)?$this->brands->name:NULL;
      $category_name = ($category_id && $this->categories)?$this->categories->name:NULL;

      if($this->image){
        $company_name = $this->companies->domain;
        $tempImagePath = "/storage/app/public/uploads/" . $company_name . '/products/' . $this->image;
      }else{
        $tempImagePath = "/storage/app/public/uploads/" . "defaultprod.jpg";
      }
      $image_path = 'https://'.$_SERVER['HTTP_HOST'].'/cms'.$tempImagePath;
      // if($variant_flag==0){
      return [
        'id' => md5($this->id),
        'product_id' => $this->id,
        'product_name' => $this->product_name,
        'variant_id' => NULL,
        'variant_name' => NULL,
        'variant_attributes' => NULL,
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
        'taxes' => $product_taxes->first()?TaxResource::collection($product_taxes):NULL,
        'image' =>  $image_path, 
        'status' => $this->status,
        'unit_conversions' => $this->conversions->count()>0?UnitConversionResource::collection($this->conversions):NULL
      ];
      // }
      // elseif($variant_flag==1){
      //   $variant_details = $this->outlet_variants;
      //   if($variant_details->first()){
      //     foreach($variant_details as $variant_detail){
      //       return [
      //         'product_id' => $this->id,
      //         'product_name' => $this->product_name,
      //         'variant_id' => $variant_detail->id,
      //         'variant_name' => $variant_detail->variant,
      //         'variant_attributes' => $variant_detail->variant_colors,
      //         'unit' => $variant_detail->unit,
      //         'unit_details' => new UnitResource($variant_detail->units),
      //         'mrp' => $variant_detail->mrp,
      //         'brand_id' => $brand_id,
      //         'brand_name' => $brand_name,
      //         'category_id' => $category_id,
      //         'category_name' => $category_name,
      //         'short_desc' => $variant_detail->short_desc,
      //         'pdiscount' => $variant_detail->discount,
      //         'pdiscount_type' => 'Amt',
      //         'min_order_qty' => $variant_detail->moq,
      //         'max_order_qty' => $variant_detail->moq,
      //         'taxes' => $product_taxes->first()?TaxResource::collection($product_taxes):NULL,
      //         'image' =>  $image_path, 
      //         'unit_conversions' => $this->conversions->count()>0?UnitConversionResource::collection($this->conversions):NULL
      //       ];
      //     }
      //   }
      // }
    }
}
