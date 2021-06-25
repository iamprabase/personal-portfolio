<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalSizeColumnsToClientSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->decimal('total_file_size_gb', 10, 2)->after('party_file_upload_size')->default(1);
          $table->decimal('total_image_size_gb', 10, 2)->after('party_image_upload_size')->default(1);
          $table->decimal('total_collaterals_size_gb', 10, 2)->after('uploadtypes')->default(1);
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
