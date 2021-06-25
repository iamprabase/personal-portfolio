<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    protected $table = "return_details";

    protected $fillable = [
        'return_id','product_id','product_name', 'variant_id','variant_name', 'unit_id', 'unit_name','unit_symbol', 'quantity', 'reason','image','image_path', 'batch_no','mfg_date', 'expiry_date'
    ];
    
}
