<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomModuleFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_module_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('custom_module_id');
            $table->unsignedInteger('user_id');
            $table->string('type');
            $table->string('title');
            $table->string('slug');
            $table->string('options')->nullable();
            $table->boolean('status');
            $table->integer('order');
            $table->boolean('required');
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
        Schema::dropIfExists('custom_module_fields');
    }
}
