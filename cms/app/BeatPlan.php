<?php

namespace App;

use App\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeatPlan extends Model
{
    use SoftDeletes;
    protected $fillable = ['employee_id', 'company_id', 'plandate', 'planenddate', 'plan_from_time', 'plan_to_time', 'title', 'party_name', 'remark', 'status'];

    // protected $table = 'beat_plans';

    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

}
