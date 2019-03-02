<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Response::class, function (Faker $faker) {
    return [
        'type' => $faker->randomElement(['text', 'phone', 'email']),
        'message' => $faker->realText,
        'call_sid' => $faker->randomNumber,
        'recording_sid' => $faker->randomNumber,
        'incoming' => $faker->randomElement([true, false])
    ];
});
