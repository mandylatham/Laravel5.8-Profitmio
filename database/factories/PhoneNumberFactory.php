<?php

use Faker\Generator as Faker;
use App\Models\Company;
use App\Models\PhoneNumber;

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

$factory->define(PhoneNumber::class, function (Faker $faker) {
    return [
        'dealership_id' => Company::where('type', 'dealership')->first()->id,
        'phone_number' => $faker->phoneNumber,
        'forward' => $faker->phoneNumber,
        'sid' => '',
        'region' => $faker->stateAbbr,
        'state' => $faker->stateAbbr,
        'zip' => $faker->postcode,
    ];
});
