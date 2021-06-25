<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldToSuperadminSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_settings', function (Blueprint $table) {
          $table->tinyInteger('allowed_party_type_levels')->default(1);
          $table->integer('user_roles')->default(2);
          $table->tinyInteger('user_hierarchy_level')->default(2);
          $table->tinyInteger('allow_party_duplication')->default(0);
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
