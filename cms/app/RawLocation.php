<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawLocation extends Model
{
    //
    public $timestamps = true;
    protected $fillable = [
        'unique_id',
        'company_id',
        'employee_id',
        'latitude',
        'longitude',
        'altitude',
        'accuracy',
        'speed',
        'speed_accuracy',
        'battery_level',
        'provider',
        'activity',
        'still',
        'unix_timestamp',
        'datetime',
        'date'
    ];

}
