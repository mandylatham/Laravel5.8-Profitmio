<?php

use Faker\Generator as Faker;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\PhoneNumber;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'agency_id' => Company::where('type', 'agency')->inRandomOrder()->first()->id,
        'dealership_id' => Company::where('type', 'dealership')->inRandomOrder()->first()->id,
        'name' => $faker->name,
        'order_id' => $faker->numberBetween(1, 10),
        'sms_on_callback' => $faker->randomElement([true, false]),
        'sms_on_callback_number' => [],
        'service_dept' =>  $faker->randomElement([true, false]),
        'service_dept_email' =>  [$faker->safeEmail],
        'adf_crm_export' => $faker->randomElement([true, false]),
        'adf_crm_export_email' => [$faker->safeEmail],
        'lead_alerts' => $faker->randomElement([true, false]),
        'lead_alert_email' => [$faker->email],
        'client_passthrough' => $faker->randomElement([true, false]),
        'client_passthrough_email' => [$faker->email],
        'starts_at' => Carbon::now()->subDays(5)->toDateTimeString(),
        'ends_at' => Carbon::now()->addDays(10)->toDateTimeString(),
        'expires_at' => Carbon::now()->addDays(20)->toDateTimeString(),
        'status' => $faker->randomElement(['Active', 'Archived', 'Completed', 'Expired', 'Upcoming'])
    ];
});
