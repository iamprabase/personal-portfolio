<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use LogsActivity,SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['unique_id', 'company_id', 'employee_id', 'check_datetime', 'adate', 'atime', 'unix_timestamp', 'check_type', 'auto_checkout', 'latitude', 'longitude', 'address', 'device'];
    
     public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Activity';
        if ($eventName == 'created')
        {
            return "Checked In";
        }

        if ($eventName == 'updated')
        {
            return "Checked Out";
        }

        if ($eventName == 'deleted')
        {
            return "Record Deleted";
        }

        return '';
    }

    public function employees()
    {
      return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
	public function scopeEmployeeId($query, int $id)
    {
        return $query->where('employee_id', $id);
    }

    public function scopeCheckType($query)
    {
        return $query->where('check_type', 1);
    }

    public function scopeAutoCheckout($query) {
        return $query->where('auto_checkout', null);
    }

    public function scopeAdate($query, $startDate, $endDate) {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('adate', [$startDate, $endDate]);
        } else {
            return $query;
        }
        
    }

}