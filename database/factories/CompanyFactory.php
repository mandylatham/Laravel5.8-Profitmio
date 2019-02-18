<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Models\Company;

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

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'type' => $faker->randomElement(['support', 'agency', 'dealership']),
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'address2' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'zip' => $faker->postcode,
        'country' => $faker->countryCode,
        'url' => $faker->url,
        'image_url' => $faker->imageUrl(),
        'facebook' => $faker->url,
        'twitter' => $faker->url,
    ];
});
