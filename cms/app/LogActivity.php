<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
class LogActivity extends \Spatie\Activitylog\Models\Activity
{
    protected $table = "activity_log";

    protected $fillable = ['ip_address','device_details','log_name', 'description', 'subject_id', 'subject_type', 'causer_id', 'properties', 'created_at', 'updated_at'];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function save(array $options = [])
    {
      if(auth()->user()){
            $this->company_id = auth()->user()->company_id;
        }
      $this->ip_address = request()->ip();
      parent::save();

      return $this;
    }
}