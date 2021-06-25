<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientCategoryRateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('client_category_rate_types', function (Blueprint $table) {
        $table->integer('client_id')->unsigned()->index();
        $table->foreign('client_id')->references('id')->on('clients');
        $table->integer('category_rate_type_id')->unsigned()->index();
        $table->foreign('category_rate_type_id')->references('id')->on('category_rate_types');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('client_category_rate_types');
    }
}
