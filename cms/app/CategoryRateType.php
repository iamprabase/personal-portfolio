<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryRateType extends Model
{
    protected $fillable = ['name', 'category_id'];
    protected $hidden = ['pivot'];

    public function appliedcategoryrates(){
      return $this->belongsToMany(Client::class, 'client_category_rate_types', 'category_rate_type_id', 'client_id');
    }

    public function itemrates(){
      return $this->hasMany(CategoryRateTypeRate::class, 'category_rate_type_id', 'id');
    }

}
