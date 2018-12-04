<?php

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        factory(Campaign::class, 20)
            ->create()
            ->each(function ($campaign) use ($faker) {
                // Create recipient lists
                factory(\App\Models\RecipientList::class, $faker->numberBetween(1, 5))
                    ->create([
                        'campaign_id' => $campaign->id
                    ])
                    ->each(function ($recipientList) use ($campaign, $faker) {
                        // Create recipients to recipient list
                        factory(\App\Models\Recipient::class, $faker->numberBetween(1, 10))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_list_id' => $recipientList->id
                            ])
                            ->each(function ($recipient) use ($campaign) {
                                factory(\App\Models\Response::class, 12)
                                    ->create([
                                        'campaign_id' => $campaign->id,
                                        'recipient_id' => $recipient->id
                                    ]);
                            });
                    });
            });
    }
}
