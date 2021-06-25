<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionExtraChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_extra_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_id')->unsigned()->index();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->enum('pricing_type', ['per_user' => 'Per User', 'fixed' => 'Fixed']);
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
        Schema::dropIfExists('subscription_extra_charges');
    }
}
