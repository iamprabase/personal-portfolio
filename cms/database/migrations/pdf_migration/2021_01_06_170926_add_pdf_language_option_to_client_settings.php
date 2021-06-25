<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPdfLanguageOptionToClientSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->string('pdf_language', 10)->after('time_zone')->default('en-ar');
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
            //
        });
    }
}
