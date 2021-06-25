<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalNameColumnToPartyUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('party_uploads', function (Blueprint $table) {
          $table->string('original_file_name', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('party_uploads', function (Blueprint $table) {
          $table->dropColumn('original_filename');  //
        });
    }
}
