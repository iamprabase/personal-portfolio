<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailerProductSetup extends Model
{
    protected $table = 'retailer_product_setup';
    protected $guarded = [];

    public function products()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function variants()
    {
        return $this->belongsTo('App\ProductVariant', 'variant_id');
    }
    
    public function companies()
    {
        return $this->belongsTo('App\Company');
    }
}
