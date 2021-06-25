<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayRemark extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function employees(){
      return $this->belongsTo('App\Employee', 'id', 'employee_id');
    }

    public function remarkdetails()
    {
        return $this->hasMany('App\DayRemarkDetail', 'remark_id','id');
    }
}