<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubscriptionHasPaymentOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_has_payment_options', function(Blueprint $table){
          $table->increments('id');
          $table->integer('subscription_id')->unsigned();
          $table->integer('payment_option_id')->unsigned();
          $table->foreign('subscription_id')->references('id')->on('subscriptions');
          $table->foreign('payment_option_id')->references('id')->on('payment_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
