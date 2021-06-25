<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderScheme extends Model
{
    protected $guarded = [];

    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }
}
