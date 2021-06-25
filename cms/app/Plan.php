<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
  protected $hidden = ['pivot'];
    public function companies()
    {
        return $this->belongsToMany('App\Company', 'company_plan', 'plan_id', 'company_id');
    }
    
    public function modules(){
      return $this->belongsToMany('App\Module', 'plan_has_modules', 'plan_id', 'module_id');
    }

    public function subscriptions(){
      return $this->hasMany('App\Subscrpion');
    }
}
