<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OdometerReport extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


    public function getStartTimeAttribute($start_time)
    {
        return getDeltaDate(date('Y-m-d', strtotime($start_time))) .' '.Carbon::parse($start_time)->format('g:i A');
    }

    public function getEndTimeAttribute($end_time)
    {
        return getDeltaDate(date('Y-m-d', strtotime($end_time))) .' '.Carbon::parse($end_time)->format('g:i A');

    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'start_time' => $this->getOriginal('start_time'),
            'end_time' =>  $this->getOriginal('end_time'),
            'company_id' => $this->company_id,
            'employee_id' => $this->employee_id,
            'uuid' => $this->uuid,
            'start_reading' => $this->start_reading,
            'end_reading' => $this->end_reading,
            'start_location' => $this->start_location,
            'end_location' => $this->end_location,
            'notes' => $this->notes,
            'distance_unit' => $this->distance_unit,
            'distance' => $this->end_reading - $this->start_reading
        ];
    }

}
