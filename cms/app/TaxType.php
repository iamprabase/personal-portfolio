<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxType extends Model
{
  use SoftDeletes; 
  protected $table = "tax_types";

  protected $guarded = [];

  public function orders()
  {
      return $this->belongsToMany('App\Order', 'tax_on_orders', 'tax_type_id', 'order_id');
  }

  public function products(){
    return $this->belongsToMany('App\Product', 'tax_on_products', 'tax_type_id', 'product_id');
  }

  public function orderdetails()
  {
      return $this->belongsToMany('App\OrderDetails', 'tax_on_orderproducts', 'tax_type_id', 'orderproduct_id');
  }

}
