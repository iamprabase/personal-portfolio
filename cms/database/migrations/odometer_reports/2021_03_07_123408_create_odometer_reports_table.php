<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOdometerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odometer_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('start_reading')->nullable();
            $table->dateTime('start_time');
            $table->string('start_location');
            $table->string('end_reading')->nullable();
            $table->dateTime('end_time');
            $table->string('end_location');
            $table->text('notes')->nullable();
            $table->string('distance')->nullable();
            $table->string('amount')->default(0); // this will be calculated based on distance and odometer-rate in client setting
            $table->boolean('distance_unit')->default(1); // 1 is km and 0 is miles
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
        Schema::dropIfExists('odometer_reports');
    }
}
