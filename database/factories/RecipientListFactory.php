<?php

use Faker\Generator as Faker;
use App\Models\RecipientList;
use App\Models\User;
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

$factory->define(RecipientList::class, function (Faker $faker) {
    return [
        'uploaded_by' => User::inRandomOrder()->first()->id,
        'name' => $faker->name,
        'recipients_added' => true,
        'fieldmap' => '',
    ];
});
