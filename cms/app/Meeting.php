<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;

class Meeting extends Model
{
    use LogsActivity;
    protected $modelName = 'Meeting';
    //
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
