<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateSetup extends Model
{
  use SoftDeletes;
  protected $table = 'rates';
  protected $guarded = [];

  public function ratedetails()
  {
      return $this->hasMany('App\RateDetail', 'rate_id', 'id')->select('product_id', 'variant_id', 'mrp');
  }

  public function clients()
  {
      return $this->hasMany('App\Client', 'rate_id', 'id');
  }
}
