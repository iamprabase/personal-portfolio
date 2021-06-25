<?php

use App\User;
use App\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
          'name' => 'DSA SuperAdmin',
          'email' => 'admin@dsa.com',
          'password' => bcrypt('password'),
          'is_appadmin' => 1,
          'is_active' => 2,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::statement("ALTER TABLE `settings` ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `deleted_at`");

        Setting::create([
          'id' => 1,
          'title' => 'DeltaSalesApp',
          'email' => 'admin@dsa.com',
          'small_logo' => 'supadmin-small-logo.png',
          'small_logo_path' => '/cms/storage/app/public/uploads/supadmin-small-logo.png',
        ]);
    }
}
