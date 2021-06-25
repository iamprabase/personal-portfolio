<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GPSTrigger extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'company_id','employee_id','status','trigger_date_time' ];
}
