<?php

use Faker\Generator as Faker;
use App\Models\Appointment;
use App\Models\Recipient;
use App\Models\Campaign;

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

$factory->define(Appointment::class, function (Faker $faker) {
    return [
        'campaign_id' => Campaign::inRandomOrder()->first()->id,
        'recipient_id' => Recipient::inRandomOrder()->first()->id,
        'type' => $faker->randomElement(['appointment', 'callback'])
    ];
});
