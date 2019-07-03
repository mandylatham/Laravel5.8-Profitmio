<?php

use Faker\Generator as Faker;

use App\Models\Drop;
use App\Models\Campaign;

$factory->define(Drop::class, function (Faker $faker) {
    return [
        'type' => $faker->randomElement([Drop::TYPE_EMAIL, Drop::TYPE_SMS]),
        'send_at' => null,
        'email_subject' => $faker->text(60),
        'email_text' => $faker->text(200),
        'email_html' => $faker->randomHtml(),
        'recipient_group' => 0,
        'text_message' => $faker->text(),
        'text_message_image' => null,
        'send_vehicle_image' => 0,
        'campaign_id' => Campaign::inRandomOrder()->first()->id,
        'status' => Drop::STATUS_PENDING,
        'percentage_complete' => 0,
        'system_id' => 2,
        'started_at' => null,
        'completed_at' => null,
    ];
});
