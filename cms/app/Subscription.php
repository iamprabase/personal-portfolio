<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = [];

    public function plans(){
      return $this->belongsTo('App\Plan');
    } 

    public function paymentoptions(){
      return $this->belongsToMany('App\SubscriptionPaymentOption', 'subscription_has_payment_options', 'subscription_id', 'payment_option_id');
    }
}
