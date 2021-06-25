<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('table_name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->boolean('status');
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->unique(['name','company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_modules');
    }
}
