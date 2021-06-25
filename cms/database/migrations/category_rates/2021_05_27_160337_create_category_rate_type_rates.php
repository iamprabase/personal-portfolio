<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryRateTypeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('category_rate_type_rates', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('category_id')->unsigned()->index();
        $table->foreign('category_id')->references('id')->on('categories');
        $table->integer('category_rate_type_id')->unsigned()->index();
        $table->foreign('category_rate_type_id')->references('id')->on('category_rate_types');
        $table->integer('product_id')->unsigned()->index();
        $table->foreign('product_id')->references('id')->on('products');
        $table->integer('product_variant_id')->unsigned()->index()->nullable();
        $table->foreign('product_variant_id')->references('id')->on('product_variants');
        $table->decimal('mrp', 10,2)->default(0);
        $table->timestamps();
        $table->softDeletes();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('category_rate_type_rates');
    }
}
