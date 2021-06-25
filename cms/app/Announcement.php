<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Announcement extends Model
{
    use LogsActivity,SoftDeletes;
    // public function employees()
    // {
        //     return $this->hasMany('App\Employee','announce_employee', 'announcement_id','employee_id');
    // }
    public function employees()
    {
        return $this->belongsToMany('App\Employee','announce_employee','announcement_id','employee_id');
    }
    
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Announcement';
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
