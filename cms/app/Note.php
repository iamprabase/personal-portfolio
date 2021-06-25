<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use LogsActivity,SoftDeletes;
    protected $modelName = 'Note';
	protected $table = "meetings"; 
    protected $guarded = [];
    
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

    public function employee()
    {
        return $this->belongsTo('App\Employee','employee_id','id');
    }

    public function images(){
      return $this->hasMany('App\Image', 'type_id', 'id')->where('type', 'notes');  
    }

    public function clients()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }
}