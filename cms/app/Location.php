<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
    public $timestamps = true;
    protected $fillable = [
        'unique_id',
        'company_id',
        'employee_id',
        'raw_latitude',
        'latitude',
        'raw_longitude',
        'longitude',
        'address',
        'altitude',
        'unix_timestamp',
        'accuracy',
        'distance_from_last_gps',
        'speed',
        'speed_accuracy',
        'battery_level',
        'activity',
        'still',
        'datetime',
        'date'
    ];

}
