<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionFreeFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_free_features', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_id')->unsigned()->index();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->string('key');
            $table->json('value');
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
        Schema::dropIfExists('subscription_free_features');
    }
}
