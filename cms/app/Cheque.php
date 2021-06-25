<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Cheque extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $modelName = 'Cheque';
    protected $fillable = ['company_id', 'cheque_date', 'name', 'receive_date', 'bank_id', 'employee_id', 'due_date', 'remarks', 'amount'];

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function employees()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id')->withTrashed();
    }

    public function getActivityDescriptionForEvent($eventName)
    {
        if ($eventName == 'created')
        {
            return 'Created '.$modelName;
        }

        if ($eventName == 'updated')
        {
            return 'Updated '.$modelName;
        }

        if ($eventName == 'deleted')
        {
            return 'Deleted '.$modelName;
        }

        return '';
    }
}