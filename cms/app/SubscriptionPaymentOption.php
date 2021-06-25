<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentOption extends Model
{
  protected $guarded = [];
  protected $table = "subscription_has_payment_options";
}
