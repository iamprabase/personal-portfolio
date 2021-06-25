<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_dependencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->unsigned()->index();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->integer('dependent_module_id')->unsigned()->index();
            $table->foreign('dependent_module_id')->references('id')->on('modules')->onDelete('cascade');
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
        Schema::dropIfExists('module_dependencies');
    }
}
