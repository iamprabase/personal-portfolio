<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSubscriptionPriceFromSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
          $table->tinyInteger('expiry_after_current_billing')->after('trial_days');
          $table->integer('auto_renewal_days')->default(0)->after('expiry_after_current_billing');
          $table->integer('payment_option_id')->unsigned()->nullable()->after('auto_renewal_days');
          $table->foreign('payment_option_id')->references('id')->on('payment_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIfExists('price');
            $table->dropIfExists('payment_option');
            $table->dropIfExists('expiry_after_current_renewal');
            $table->dropIfExists('auto_renewal_time');
        });
    }
}
