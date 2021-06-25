<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class ProductReturn extends Model{
    use LogsActivity;
    protected $table = "returns";

    protected $modelName = 'Return';

    protected $fillable = [
        'company_id','employee_id', 'client_id', 'superior', 'return_date','return_unixtime'
    ];

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
