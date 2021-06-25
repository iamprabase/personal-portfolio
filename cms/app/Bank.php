<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
	use SoftDeletes;

	 protected $fillable = ['name' , 'company_id'];

    public function cheques()
    {
        return $this->hasMany('App\Collection', 'bank_id','id');
    }
}
