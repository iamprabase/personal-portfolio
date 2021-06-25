<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = ['company_id', 'product_id','variant', 'variant_colors', 'mrp', 'unit','short_desc','moq','app_visibility'];

    public function products()
    {
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }

    public function orderproducts()
    {
        return $this->hasMany('App\OrderDetails');
    }

    public function colors()
    {
        return $this->belongsToMany('App\Color');
    }

    public function companies()
    {
        return $this->hasOne('App\Company');
    }

    public function units()
    {
        return $this->belongsTo('App\UnitTypes', 'unit', 'id');
    }
}
