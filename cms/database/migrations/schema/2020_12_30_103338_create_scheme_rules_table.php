<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('scheme_id');
            $table->string('brand_name')->nullable();
            $table->string('category_name')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->json('product_id')->nullable();
            $table->json('product_variant')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('offered_qty')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('discount_amount')->nullable();
            $table->bigInteger('percentage_off')->nullable();
            $table->unsignedBigInteger('offered_product')->nullable();
            $table->unsignedBigInteger('offered_product_variant')->nullable();
            $table->boolean('product_option')->nullable();
            $table->string('option');
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
        Schema::dropIfExists('scheme_rules');
    }
}
