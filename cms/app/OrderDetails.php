<?php


namespace App;


use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrderDetails extends Model

{


    //Table Name
    use SoftDeletes;

    protected $table = 'orderproducts';
    protected $fillable = [
        'id','order_id', 'product_id', 'product_name', 'mrp', 'brand', 'unit', 'unit_name',
        'unit_symbol', 'rate', 'quantity', 'amount','pdiscount','pdiscount_type','ptotal_amt','short_desc','product_variant_id','product_variant_name','variant_colors'
    ];


    //Primary Key


    public $primaryKey = 'id';


    //Timestamps


    public $timestamps = true;


    public function order()
    {

        return $this->belongsTo('App\Order');


    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function product_variant()
    {
        return $this->belongsTo('App\ProductVariant', 'product_variant_id', 'id');
    }

    public function taxes()
    {
        return $this->belongsToMany('App\TaxType', 'tax_on_orderproducts', 'orderproduct_id', 'tax_type_id');
    }
    
    public function brands()
    {
        return $this->belongsTo('App\Brand', 'brand', 'id');
    }
}