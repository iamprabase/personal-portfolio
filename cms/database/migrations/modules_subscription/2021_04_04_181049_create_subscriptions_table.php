<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subscription_code');
            $table->integer('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->string('name');
            $table->string('domain')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->integer('min_users')->default(1);
            $table->decimal('price_per_user', 8, 2)->default(0);
            $table->decimal('setup_fee', 8, 2)->default(0);
            $table->integer('trial_days')->default(0);
            $table->tinyInteger('expiry_after_current_billing')->after('trial_days');
            $table->integer('auto_renewal_days')->default(0)->after('expiry_after_current_billing');
            $table->integer('payment_option_id')->unsigned()->nullable()->after('auto_renewal_days');
            $table->foreign('payment_option_id')->references('id')->on('payment_options')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
