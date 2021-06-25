<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGPSTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_p_s_triggers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('employee_id');
            $table->string('status');
            $table->dateTime('triggerDateTime');
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
        Schema::dropIfExists('g_p_s_triggers');
    }
}
