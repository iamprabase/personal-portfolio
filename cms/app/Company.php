<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * Get Company customers
     */
    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    /**
     * Get Company managers
     */
    public function managers()
    {
        return $this->hasMany('App\Manager');
    }

    /**
     * Get Company owners
     */
    public function owners()
    {
        return $this->hasMany('App\Manager')->where('is_owner', '=', '1');
    }

    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function activeemployees()
    {
        return $this->hasMany('App\Employee')->where('status', 'Active');
    }

    public function activitylogged()
    {
        return $this->hasMany('App\LogActivity');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Plan', 'company_plan', 'company_id', 'plan_id');
    }


    public function clientsettings()
    {
        return $this->hasOne('App\ClientSetting');
    }

    public function orders()
    {
        return $this->hasMany('App\Order')->orderBy('id', 'desc');
    }

    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    public function outlets()
    {
        return $this->belongsToMany('App\Outlet', 'company_outlet', 'company_id', 'outlet_id');
    }

    public function retailer_products()
    {
        return $this->hasMany('App\Product')->whereVariantFlag(0)->whereStatus("Active")->whereAppVisibility(1);
    }

    public function retailer_product_variants()
    {
        return $this->hasMany('App\ProductVariant', 'company_id', 'id')->whereAppVisibility(1);
    }

    public function taxes()
    {
        return $this->hasMany('App\TaxType', 'company_id', 'id');
    }

    public function default_taxes()
    {
        return $this->hasMany('App\TaxType', 'company_id', 'id')->where('default_flag', 1);
    }

    public function order_statuses()
    {
        return $this->hasMany('App\ModuleAttribute');
    }

    public function collections()
    {
        return $this->hasMany('App\Collection', 'company_id', 'id');
    }
}
