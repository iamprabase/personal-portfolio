<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;
use Spatie\Activitylog\Traits\LogsActivity;
class BeatVPlan extends Model
{
    use LogsActivity;
   protected $table = 'beatvplans';

   
   protected $fillable = ['unique_id','employee_id','company_id','status'];
   
    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }
    
    public function beatplansdetail()
    {
        return $this->hasMany(BeatPlansDetails::class, 'beatvplan_id','id');
    }
    
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'BeatPlan';
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
