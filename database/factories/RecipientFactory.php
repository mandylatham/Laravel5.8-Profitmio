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
//        'campaign_id' => Campaign::inRandomOrder()->first()->id,
//        'recipient_list_id' => RecipientList::inRandomOrder()->first()->id,
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email
    ];
});
