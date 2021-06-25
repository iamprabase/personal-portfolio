<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientVisitPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('client_visits', function (Blueprint $table) {
          $table->bigIncrements('id')->autoIncrement();
          $table->integer('company_id');
          $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
          $table->integer('employee_id');
          $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
          $table->integer('client_id');
          $table->foreign('client_id')->references('id')->on('clients');
          $table->time('start_time');
          $table->time('end_time');
          $table->integer('visit_purpose_id');
          $table->foreign('visit_purpose_id')->references('id')->on('visit_purposes');
          $table->text('comments')->nullable();
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
      Schema::drop('client_visits');
    }
}
