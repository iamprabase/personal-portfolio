<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    // use SoftDeletes;

    // protected $dates = ['deleted_at'];

    protected $fillable = ['company_id', 'name', 'parent', 'desc', 'status'];

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function categoryrates(){
      return $this->hasMany(CategoryRateType::class, 'category_id', 'id');
    } 

    public function scopeCompanyId($query, $companyId) {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompanyCategories($query, $cols, $load_relations = array()) {
      $categories = $query->whereCompanyId(config('settings.company_id'))->orderBy('name', 'asc')->get($cols);
      if(!empty($load_relations)) {
        foreach($load_relations as $relation){
          $categories->load($relation);
        }
      }
      
      return $categories;
    }

    public function scopeFindOrFailById($query, $id, $cols) {
      $categories = $query->findOrFail($id);
      $categories->load('categoryrates');
      
      return $categories;
    }

    // public function scopecategoryWithRates($query, $cols, $load_relations = array()) {
    //   $categories = $query->with('categoryrates')->whereCompanyId(config('settings.company_id'))->select($cols);
      
    //   return $categories;
    // }
}
