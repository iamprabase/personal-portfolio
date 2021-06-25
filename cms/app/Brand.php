<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{

    protected $fillable = ['company_id', 'name', 'desc', 'status'];

    public function products()
    {
        return $this->hasMany('App\Product','brand');
    }

    public function scopeCompanyId($query, $companyId) {
        return $query->where('company_id', $companyId);
    }

}
