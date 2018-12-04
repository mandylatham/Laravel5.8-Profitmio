<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Response::class, function (Faker $faker) {
    return [
        'recipient_id' => \App\Models\Recipient::inRandomOrder()->first()->id,
        'type' => $faker->randomElement(['text', 'phone', 'email']),
        'message' => $faker->realText,
        'call_sid' => $faker->word,
        'recording_sid' => $faker->word,
        'recording_url' => $faker->word,
        'duration' => 0
    ];
});
