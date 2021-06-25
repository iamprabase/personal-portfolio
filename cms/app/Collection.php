<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Collection extends Model
{
    use SoftDeletes;
    use LogsActivity;

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function employees()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function orders() {
        return $this->hasMany('App\Order', 'id', 'order_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Collection';
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

    public function scopePaymentDate($query, $startDate, $endDate)
    {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('payment_date', [$startDate, $endDate]);
        } else {
            return $query;
        }
    }

    public function scopeClientId($query, int $id)
    {
        return $query->where('client_id', $id);
    }

    public function scopePaymentStatus($query)
    {
        return $query->where('payment_status', "Cleared");
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
    public function images(){
      return $this->hasMany('App\Image', 'type_id', 'id')->where('type', 'collection');  
    }
}
