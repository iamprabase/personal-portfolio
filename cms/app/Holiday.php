<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'description', 'start_date', 'end_date', 'company_id'
    ];
} 