<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoOrder extends Model
{
    use LogsActivity;
    use SoftDeletes;
    public function clients()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function employees()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Zero order';
        if ($eventName == 'created')
        {
            return "Created $modelName";
        }

        if ($eventName == 'updated')
        {
            return "Updated $modelName";
        }

        if ($eventName == 'deleted')
        {
            return "Deleted $modelName";
        }

        return '';
    }
	public function scopeEmployeeId($query, int $id)
    {
        return $query->where('employee_id', $id);
    }

    public function scopeDate($query, $startDate, $endDate) {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            return $query;
        } 
    }

    public function scopeCreatedAt($query, $startDate, $endDate)
    {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            return $query;
        }
    }

    public function images(){
      return $this->hasMany('App\Image', 'type_id', 'id')->where('type', 'noorders'); 
    }
}
