<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Response::class, function (Faker $faker, $type = null) {
    $type = $type ?: $faker->randomElement(['text', 'phone', 'email']);

    return [
        'type' => $type,
        'message' => $type != 'phone' ? $faker->realText : null,
        'call_sid' => $type == 'phone' ? $faker->randomNumber : null,
        'recording_sid' => $type == 'phone' ? $faker->randomNumber : null,
        'incoming' => $faker->randomElement([true, false]),
    ];
});
