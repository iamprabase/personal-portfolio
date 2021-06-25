<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToCustomModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_modules', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id');
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
        Schema::table('custom_modules', function (Blueprint $table) {
            $table->dropColumn('employee_id');
            $table->dropColumn('deleted_at');
        });
    }
}
