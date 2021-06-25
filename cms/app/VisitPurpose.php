<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitPurpose extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $guarded = [];

    public function client_visit()
    {
        return $this->hasMany('App\ClientVisit');
    }

}
