<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessType extends Model
{
	use SoftDeletes;
    public function clients()
    {
        return $this->hasMany('App\Client', 'business_id','id');
    }
}
