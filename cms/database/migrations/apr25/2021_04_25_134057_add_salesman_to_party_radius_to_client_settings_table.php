<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesmanToPartyRadiusToClientSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->unsignedBigInteger('salesman_to_party_radius')->default(100);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->dropColumnIfExists('salesman_to_party_radius');
        });
    }
}
