<?php

use Faker\Generator as Faker;
use App\Models\RecipientList;
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

$factory->define(Recipient::class, function (Faker $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
//        'last_responded_at' => \Carbon\Carbon::now('UTC'),
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'address1' => $faker->streetAddress,
        'address2' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'email_valid' => true,
        'phone_valid' => true,
        'status' => 'open-lead',
    ];
});
