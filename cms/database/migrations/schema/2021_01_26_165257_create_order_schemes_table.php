<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSchemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_schemes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('scheme_id');
            $table->unsignedBigInteger('company_id');
            $table->boolean('is_amount')->default(0);
            $table->integer('discount_amount')->nullable();
            $table->integer('free_item')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
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
        Schema::dropIfExists('order_schemes');
    }
}
