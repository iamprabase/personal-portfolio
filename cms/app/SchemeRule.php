<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeRule extends Model
{
    protected $guarded = [];

    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }



}
