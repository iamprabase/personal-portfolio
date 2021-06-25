<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;
    public $timestamps = true;
    protected $fillable = ['unique_id', 'company_id', 'employee_id', 'start_date', 'end_date', 'leavetype', 'leave_desc','status'];
    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
    public function approvedby()
    {
        return $this->belongsTo('App\Employee', 'approved_by', 'id');
    }
    public function leave_type()
    {
        return $this->belongsTo('App\LeaveType', 'leavetype', 'id')->withTrashed();
    }
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Leave';
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

    public function scopeStatus($query)
    {
        return $query->where('status', "Approved");
    }

    public function scopeCreatedAt($query, $startDate, $endDate)
    {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            return $query;
        }
    }
}
