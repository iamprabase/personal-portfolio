<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('client_id');
            $table->integer('employee_id');
            $table->integer('product_id');
            $table->integer('variant_id')->nullable();
            $table->integer('unit_id');
            $table->bigInteger('quantity');
            $table->decimal('mrp', 10,2);
            $table->decimal('total_amount', 12,2);
            $table->string('batch_no')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('reason');
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
        Schema::dropIfExists('returns');
    }
}
