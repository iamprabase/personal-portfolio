<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SchemeType extends Model
{
    protected $guarded = [];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = ucfirst($name);
        $this->attributes['slug'] = Str::slug($name);
    }
}
