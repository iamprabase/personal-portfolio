<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    protected $guarded = [];
//
//    protected $casts = [
//        'parties' => 'array',
//    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->company_id = config('settings.company_id');
            $model->status = 1;
            $model->user_id = auth()->id();
            $model->employee_id = auth()->user()->EmployeeId();
        });
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] == 1 ? 'Active' : 'Inactive';
    }


    public function schemeTypes()
    {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }

    public function schemeRule()
    {
        return $this->hasOne(SchemeRule::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getMinDateAttribute()
    {
        if (Carbon::today()->toDateString() >= Carbon::parse($this->start_date)->toDateString()) {
            return Carbon::parse($this->start_date)->format('M d Y');
        }
        return Carbon::today()->format('M d Y');
    }

}
