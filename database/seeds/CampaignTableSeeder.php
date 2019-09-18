<?php

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CampaignTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Active Campaigns
        factory(Campaign::class, 15)
            ->create([
                'status' => 'Active'
            ])
            ->each(function ($campaign) {
                $this->processCampaign($campaign);
            });
        // Upcoming Campaigns
        factory(Campaign::class, 15)
            ->create([
                'status' => 'Upcoming',
                'starts_at' => Carbon::now()->addDays(5)->toDateTimeString(),
                'ends_at' => Carbon::now()->addDays(15)->toDateTimeString(),
                'expires_at' => Carbon::now()->addDays(25)->toDateTimeString(),
            ])
            ->each(function ($campaign) {
                $this->processCampaign($campaign);
            });
    }
    public function processCampaign($campaign)
    {
        $faker = Faker\Factory::create();
        $dealership = $campaign->dealership;
        $agency = $campaign->agency;
        // Attach users to campaign
        $users = $dealership->users()->where('company_user.role', 'user')->inRandomOrder()->take(5)->get();
        $users->concat($agency->users()->where('company_user.role', 'user')->inRandomOrder()->take(5)->get());
        foreach ($users as $user) {
            \App\Models\CampaignUser::insert([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id
            ]);
        }
        // Create recipient lists
        factory(\App\Models\RecipientList::class, $faker->numberBetween(1, 3))
            ->create([
                'campaign_id' => $campaign->id
            ])
            ->each(function ($recipientList) use ($campaign, $faker) {
                // Attach recipients to recipient list
                factory(\App\Models\Recipient::class, $faker->numberBetween(1, 20))
                    ->create([
                        'campaign_id' => $campaign->id,
                        'recipient_list_id' => $recipientList->id
                    ])
                    ->each(function ($recipient) use ($campaign, $faker) {
                        //  Attach emails
                        factory(\App\Models\Response::class, $faker->numberBetween(5, 15))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'email'
                            ]);
                        //  Attach text
                        factory(\App\Models\Response::class, $faker->numberBetween(5, 15))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'text'
                            ]);
                        //  Attach phone
                        factory(\App\Models\Response::class, $faker->numberBetween(0, 3))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'phone'
                            ]);
                    });
            });
    }
}
