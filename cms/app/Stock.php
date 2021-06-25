<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = "stocks";

    protected $fillable = [
        'company_id','employee_id', 'client_id', 'stock_date','stock_date_unix'
    ];
}
