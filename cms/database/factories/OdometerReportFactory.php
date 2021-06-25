<?php

use Faker\Generator as Faker;

$factory->define(\App\OdometerReport::class, function (Faker $faker) {
    $reading = rand(10, 50);
    $endReading = $reading + rand(10, 30);
    return [
        'company_id' => 195,
        'employee_id' => \App\Employee::where('company_id', 195)->where('status', 'Active')->get()->random()->id,
        'start_reading' => $reading,
        'end_reading' => $endReading,
        'distance' => $endReading - $reading,
        'start_time' => \Carbon\Carbon::now(),
        'end_time' => \Carbon\Carbon::now()->addHours(5),
        'start_location' => $faker->address,
        'end_location' => $faker->address,
        'notes' => $faker->text,
        'created_at' => \Carbon\Carbon::now(),
        'date' => \Carbon\Carbon::today()->addDay(rand(1,10))->format('Y-m-d')
    ];
});
