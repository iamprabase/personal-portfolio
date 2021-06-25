<?php

namespace App;

use App\Category;
use App\OrderDetails;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use LogsActivity;
    protected $modelName = 'Product';
    use SoftDeletes;

    protected $fillable = ['company_id', 'product_name', 'category_id', 'brand','mrp','unit','details', 'status','product_code'];

    // protected $dates = ['deleted_at'];

    public function categories()
    {
        return $this->belongsTo('App\Category', 'category_id', 'id');
    }

    public function categoryrates()
    {
      return $this->hasMany('App\CategoryRateTypeRate', 'product_id', 'id')->whereNull('product_variant_id');
    }

    public function variantcategoryrates()
    {
      return $this->hasMany('App\CategoryRateTypeRate', 'product_variant_id', 'variant_id')->whereNotNull('product_variant_id');
    }

    public function orderproducts()
    {
        return $this->hasMany('App\OrderDetails');
    }
    
    public function product_variants()
    {
        return $this->hasMany('App\ProductVariant');
    }

    public function brands()
    {
        return $this->belongsTo('App\Brand', 'brand', 'id');
    }

    public function taxes()
    {
        return $this->belongsToMany('App\TaxType', 'tax_on_products', 'product_id', 'tax_type_id');
    }

    public function conversions()
    {
        return $this->belongsToMany('App\UnitConversion', 'product_unit_conversion', 'product_id', 'unit_conversion_id');
    }

    public function getActivityDescriptionForEvent($eventName)
    {
        if ($eventName == 'created')
        {
            return 'Created '.$modelName;
        }

        if ($eventName == 'updated')
        {
            return 'Updated '.$modelName;
        }

        if ($eventName == 'deleted')
        {
            return 'Deleted '.$modelName;
        }

        return '';
    }

    public function companies()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function outlet_variants()
    {
        return $this->hasMany('App\ProductVariant')->whereAppVisibility(1);
    }

    public function units()
    {
        return $this->belongsTo('App\UnitTypes', 'unit', 'id');
    }

    public function product_rates()
    {
        return $this->hasMany('App\RateDetail', 'product_id', 'id')->whereNULL('variant_id');
    }

    public function product_variant_rates()
    {
        return $this->hasMany('App\RateDetail', 'variant_id', 'variant_id');
    }

    public function stockdetails()
    {
        return $this->hasMany('App\StockDetail');
    }

    public function scopeAllProductsAtVariantLevel( $query, $cols, $status='Active', $extraParams = array(), $category ){
      
      $productQuery = $query->where('products.company_id', config('settings.company_id'))
                  ->where('products.status', $status)
                  ->leftJoin('product_variants', function ($join) {
                      $join->on('products.id',  '=', 'product_variants.product_id')
                          ->where('products.variant_flag', '=', 1);
                  })->where(function($query) use($category) {
                    if($category) $query->where('products.category_id', $category);
                  })
                  ->where('product_variants.deleted_at', NULL)
                  ->leftJoin('unit_types', function($query){
                    $query->on('unit_types.id', '=', DB::raw('case when products.variant_flag = 0 then products.unit else product_variants.unit end'));
                  })
                  ->orderBy('products.star_product', 'desc');
      if(empty($extraParams)){
        $products =  $productQuery->get($cols);
        return $products;
      }

      $return = array();
      $searchParamas = $extraParams['search']; 
      if(isset($searchParamas)){
        $productQuery = $productQuery->where(function($query) use ($searchParamas){
          $query->orWhere('products.product_name', 'LIKE', "%{$searchParamas}%");
          $query->orWhere('unit_types.name', 'LIKE', "%{$searchParamas}%");
          $query->orWhere('products.mrp', 'LIKE', "%{$searchParamas}%");
          $query->orWhere('product_variants.variant', 'LIKE', "%{$searchParamas}%");
        });
      }
      $order = $extraParams['order']; 
      $dir = $extraParams['dir']; 
      if($order=="product_name"){
        $productQuery = $productQuery->orderBy($order,$dir)->orderBy('product_variants.variant',$dir);
      }else{
        $productQuery = $productQuery->orderBy($order,$dir);
      }
      $offset = $extraParams['offset']; 
      $limit = $extraParams['limit'];
      if(isset($offset) && isset($limit)){
        $totalData = $productQuery->count();
        $totalFiltered = $productQuery->count();
        if($limit==-1) $limit = $totalFiltered;  
        
        $products =  $productQuery->offset($offset)
                          ->limit($limit)
                          ->get($cols);

                          
        $return["data"] = $products; 
        $return["currentFilter"] =  $totalFiltered; 
        $return["totalData"] = $totalData;
      } 

      return $return;
    }
}
