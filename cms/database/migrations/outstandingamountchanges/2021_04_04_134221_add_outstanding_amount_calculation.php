<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutstandingAmountCalculation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->tinyInteger('outstanding_amt_calculation')->default(0)->after('order_with_amt');
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
          $table->dropColumnIfExists('outstanding_amt_calculation');
        });
    }
}
