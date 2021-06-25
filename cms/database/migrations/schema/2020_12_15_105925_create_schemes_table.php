<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('scheme_type_id');
            $table->string('name');
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('status')->default(1);
            $table->string('image')->nullable();
            $table->string('option')->nullable();
            $table->json('product_id')->nullable();
            $table->json('product_variant')->nullable();
            $table->unsignedBigInteger('offered_product')->nullable();
            $table->unsignedBigInteger('offered_product_variant')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('offered_qty')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('discount_amount')->nullable();
            $table->bigInteger('percentage_off')->nullable();
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
        Schema::dropIfExists('product_schemas');
    }
}
