<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityPriority extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'company_id'
    ];

    public function activities()
    {
        return $this->hasMany('App\Activity', 'priority','id');
    }
}
