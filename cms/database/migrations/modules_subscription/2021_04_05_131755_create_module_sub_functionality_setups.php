<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSubFunctionalitySetups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('module_functionality_setups', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('module_id')->unsigned()->index();
        $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        $table->string('key')->unique();
        $table->json('value');
        $table->string('type');
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
        //
    }
}
