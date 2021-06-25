<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulePricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_pricing', function(Blueprint $table){
          $table->increments('id');
          $table->integer('module_id')->unsigned()->index();
          $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
          $table->decimal('usd_price')->default(0);
          $table->decimal('inr_price')->default(0);
          $table->decimal('npr_price')->default(0);
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
      Schema::dropIfExists('module_pricing');
    }
}
