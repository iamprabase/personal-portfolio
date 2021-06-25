<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
        DB::table('scheme_types')->insert([
            [
                'id' => 1,
                'name' => 'QPS 1 : On buying certain units of specific products, get free products',
                'slug' => 'qps-1-on-buying-certain-units-of-specific-products-get-free-products',

            ],
            [
                'id' => 2,
                'name' => 'QPS 2 : On buying certain units of specific products, get percentage discount',
                'slug' => 'qps-2-on-buying-certain-units-of-specific-products-get-percentage-discount',

            ],
            [
                'id' => 3,
                'name' => 'QPS 3 : On buying certain units of specific products, get discount in the amount',
                'slug' => 'qps-3-on-buying-certain-units-of-specific-products-get-discount-in-the-amount',

            ],
            [
                'id' => 4,
                'name' => 'VPS 1: On overall purchase of certain value, get percentage discount',
                'slug' => 'vps-1-on-overall-purchase-of-certain-value-get-percentage-discount',

            ],
            [
                'id' => 5,
                'name' => 'VPS 2: : On overall purchase of certain value, get discount in the amount',
                'slug' => 'vps-2-on-overall-purchase-of-certain-value-get-discount-in-the-amount',

            ],
            [
                'id' => 6,
                'name' => 'VPS 3: On overall purchase of certain value, get free products',
                'slug' => 'vps-3-on-overall-purchase-of-certain-value-get-free-products',

            ]
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_types');
    }
}
