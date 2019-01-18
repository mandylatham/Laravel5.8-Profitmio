<?php

use App\Models\CampaignScheduleTemplate;
use Faker\Generator as Faker;

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

$factory->define(CampaignScheduleTemplate::class, function (Faker $faker) {
    $type = $faker->randomElement(['email', 'sms']);
    $subject = $faker->catchPhrase;

    return [
        'name' => $faker->catchPhrase,
        'type' => $type,
        'email_subject' => ($type == 'email' ? $subject : ''),
        'email_html' => ($type == 'email' ? $faker->randomHtml(2,3) : ''),
        'email_text' => ($type == 'email' ? $subject : ''),
        'text_message' => ($type == 'sms' ? $faker->text : ''),
    ];
});
