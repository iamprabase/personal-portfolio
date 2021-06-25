<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // $table->integer('parent_module_id')->nullable()->unsigned()->index();
            $table->integer('position')->unsigned();
            $table->string('field');
            $table->mediumText('description')->nullable();
            $table->timestamps();
          });
          
          // Schema::table('modules',function (Blueprint $table){
          //   $table->foreign('parent_module_id')->references('id')->on('modules')->onDelete('set null');
          // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
