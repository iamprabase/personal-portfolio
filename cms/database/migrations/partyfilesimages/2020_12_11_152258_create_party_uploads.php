<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartyUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('party_uploads', function (Blueprint $table) {
          $table->engine = 'InnoDB';
          $table->bigIncrements('id');
          $table->bigInteger('party_upload_folder_id')->unsigned()->index();
          $table->integer('client_id')->unsigned()->index();
          $table->integer('employee_id')->unsigned()->index();
          $table->foreign('party_upload_folder_id')->references('id')->on('party_upload_folders')->onDelete('cascade');
          $table->foreign('client_id')->references('id')->on('clients');
          $table->foreign('employee_id')->references('id')->on('employees');
          $table->string('original_file_name', 191);
          $table->string('file_name', 191);
          $table->longText('url');
          $table->string('extension', 100)->nullable();
          $table->integer('file_size')->unsigned()->nullable();
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
        //
    }
}
