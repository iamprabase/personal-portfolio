<?php

namespace App;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

//use Illuminate\Database\Eloquent\SoftDeletes;

class UnitTypes extends Model
{
    use SoftDeletes;

    //protected $table = 'unit_types';

    protected $fillable = ['company_id', 'name', 'symbol', 'status'];


    //protected $dates = ['deleted_at'];
  public function conversionunits()
  {
      return $this->hasMany('App\UnitConversion', 'unit_type_id', 'id');
  }

  public function convertedunits()
  {
      return $this->hasMany('App\UnitConversion', 'converted_unit_type_id', 'id');
  }

  public function scopeCompanyId($query, $companyId) {
      return $query->where('company_id', $companyId);
  }

}
