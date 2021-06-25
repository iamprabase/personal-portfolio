<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class BeatPlansDetails extends Model
{
   protected $table = 'beatplansdetails';
   protected $guarded = [];

     public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');        
    }

    public function beat()
    {
        return $this->belongsTo('App\Beat');
    }

    public function beatvplan()
    {
        return $this->belongsTo(BeatVPlan::class);
    }
	public function scopePlandate($query, $value1, $value2)
    {
        if (isset($value1) && isset($value2)) {
            return $query->whereBetween('plandate', [$value1, $value2]);
        } else {
            return $query;
        }
    }
}
