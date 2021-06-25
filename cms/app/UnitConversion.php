<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class UnitConversion extends Model
{
  use SoftDeletes;

  protected $table = 'unit_conversions';
  public $timestamps = true;
  protected $guarded = [];

  public function conversionunittypes()
  {
      return $this->belongsTo('App\UnitTypes', 'unit_type_id', 'id');
  }

  public function convertedunittypes()
  {
      return $this->belongsTo('App\UnitTypes', 'converted_unit_type_id', 'id');
  }

  public function conversions()
  {
      return $this->hasMany(self::class, 'unit_type_id', 'unit_type_id');
  }

  public function converted()
  {
      return $this->hasMany(self::class, 'converted_unit_type_id', 'unit_type_id');
  }

  public function products()
  {
      return $this->belongsToMany('App\Product', 'product_unit_conversion', 'unit_conversion_id', 'product_id');
  }
}
