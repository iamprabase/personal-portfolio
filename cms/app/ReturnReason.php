<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnReason extends Model
{
    protected $table = "returnreasons";
    
    protected $fillable = [
        'name', 'company_id'
    ];
}
