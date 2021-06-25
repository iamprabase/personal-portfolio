<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartyFilesImagesToClientSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
            $table->tinyInteger('party_files_images')->after('product_level_discount')->default(0);
            $table->string('party_file_upload_types', 191)->after('party_files_images')->nullable();
            $table->integer('party_file_upload_size')->after('party_file_upload_types')->unsigned()->nullable();
            $table->string('party_image_upload_types', 191)->after('party_file_upload_size')->nullable();
            $table->integer('party_image_upload_size')->after('party_image_upload_types')->unsigned()->nullable();
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
            $table->dropColumn('party_files_images');
        });
    }
}
