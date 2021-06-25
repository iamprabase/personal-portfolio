<?php

use App\PaymentOption;
use Illuminate\Database\Seeder;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentOption::insert(
          [[
            'name' => "Bank Transfer"
          ],
          [
            'name' => "To Checkout"
          ],
          [
            'name' => "Himalayan Bank Limited"
          ]]
        );
    }
}
