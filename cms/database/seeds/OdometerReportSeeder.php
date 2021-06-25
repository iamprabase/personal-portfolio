<?php

use Illuminate\Database\Seeder;

class OdometerReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\OdometerReport::class, 20)->create();
    }
}
