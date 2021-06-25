<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientVisit extends Model
{
    use SoftDeletes;
    protected $table = "client_visits";
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array 
     */
    protected $guarded = [];

    public function client()
    {
      return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function employee()
    {
      return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    public function visitpurpose()
    {
        return $this->belongsTo('App\VisitPurpose', 'visit_purpose_id', 'id')->withTrashed();
    }

    public function images(){
      return $this->hasMany('App\Image', 'type_id', 'id')->where('type', 'client_visits');  
    }


}
