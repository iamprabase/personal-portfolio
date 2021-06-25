<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    protected $table = "stock_details";

    protected $fillable = [
        'stock_id','product_id','product_name', 'variant_id','variant_name', 'unit_id', 'unit_name','unit_symbol', 'quantity', 'image','image_path', 'mrp', 'total_amount', 'batch_no','mfg_date', 'expiry_date'
    ];
}
