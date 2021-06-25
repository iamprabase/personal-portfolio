<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;

class TourPlan extends Model
{
    use LogsActivity;

    protected $table = "tourplans";
    protected $fillable = [
        'unique_id','company_id','employee_id','start_date', 'end_date', 'visit_place', 'visit_purpose', 'status'
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Tour plan';
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

    public function employee(){
      return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
}
