<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LeaveType extends Model
{
	use SoftDeletes;
    protected $table = 'leave_type';

    public function leaves()
    {
        return $this->hasMany('App\Leave', 'leavetype','id');
    }

}
