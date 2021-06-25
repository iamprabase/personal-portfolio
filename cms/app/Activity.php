<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Activity extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $fillable = [
        'type','unique_id', 'title', 'note', 'start_datetime', 'duration', 'priority', 'created_by',
        'assigned_to', 'client_id', 'completion_datetime', 'company_id', 'completed_by'
    ];

    public function assignedTo()
    {
        return $this->belongsTo('App\Employee', 'assigned_to', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function createdByEmployee()
    {
        return $this->belongsTo('App\Employee', 'created_by', 'id');
    }

    public function completedByEmployee()
    {
        return $this->belongsTo('App\Employee', 'completed_by', 'id');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function activityType()
    {
        return $this->belongsTo('App\ActivityType', 'type', 'id')->withTrashed();
    }

    public function activityPriority()
    {
        return $this->belongsTo('App\ActivityPriority', 'priority', 'id')->withTrashed();
    }


    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Activity';
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

}
