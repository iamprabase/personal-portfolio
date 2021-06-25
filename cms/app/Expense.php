<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity,SoftDeletes;
    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo('App\Employee', 'approved_by', 'id');
    }

    public function exptype()
    {
        return $this->belongsTo('App\ExpenseType', 'expense_type_id', 'id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Expense';
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
    public function client()
    {
        return $this->belongsTo('App\Client','client_id','id');
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
  
  public function images(){
    return $this->hasMany('App\Image', 'type_id', 'id')->where('type', 'expense'); 
  }
}
