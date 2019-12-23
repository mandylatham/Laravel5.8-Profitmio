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
        $this->command->getOutput()->progressStart(30);

        // Upcoming Campaigns
        factory(Campaign::class, 15)
            ->create([
                'ends_at' => Carbon::now()->addDays(15)->toDateTimeString(),
                'expires_at' => Carbon::now()->addDays(25)->toDateTimeString(),
                'starts_at' => Carbon::now()->addDays(5)->toDateTimeString(),
                'status' => 'Upcoming',
            ])
            ->each(function ($campaign) {
                $this->command->getOutput()->progressAdvance();
                $this->processCampaign($campaign);
            });

        // Active Campaigns
        factory(Campaign::class, 15)
            ->create([
                'ends_at' => Carbon::now()->subDays(10)->toDateTimeString(),
                'expires_at' => Carbon::now()->subDays(5)->toDateTimeString(),
                'starts_at' => Carbon::now()->subDays(25)->toDateTimeString(),
                'status' => 'Active',
            ])
            ->each(function ($campaign) {
                $this->command->getOutput()->progressAdvance();
                $this->processCampaign($campaign);
            });

        Artisan::call('leads:calculate-last-status');
        $this->command->getOutput()->progressFinish();
    }

    public function processCampaign(Campaign $campaign)
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
                // Attach new recipients to recipient list
                factory(\App\Models\Recipient::class, $faker->numberBetween(1, 20))
                    ->create([
                        'campaign_id' => $campaign->id,
                        'recipient_list_id' => $recipientList->id,
                        'status' => 'new-lead',
                    ])
                    ->each(function ($recipient) use ($campaign, $faker) {
                        //  Attach emails
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 2))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'email',
                                'incoming' => true,
                            ]);
                        //  Attach text
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 2))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'text',
                                'incoming' => true,
                            ]);
                        //  Attach phone
                        factory(\App\Models\Response::class, $faker->numberBetween(0, 1))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'phone',
                                'incoming' => true,
                            ]);
                    });
                // Attach open recipients to recipient list
                factory(\App\Models\Recipient::class, $faker->numberBetween(1, 20))
                    ->create([
                        'campaign_id' => $campaign->id,
                        'recipient_list_id' => $recipientList->id,
                        'status' => 'open-lead',
                    ])
                    ->each(function ($recipient) use ($campaign, $faker) {
                        //  Attach emails
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 15))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'email'
                            ]);
                        //  Attach text
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 15))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'text'
                            ]);
                        //  Attach phone
                        factory(\App\Models\Response::class, $faker->numberBetween(0, 2))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'phone'
                            ]);
                    });
                // Attach closed recipients to recipient list
                factory(\App\Models\Recipient::class, $faker->numberBetween(0, 3))
                    ->create([
                        'campaign_id' => $campaign->id,
                        'recipient_list_id' => $recipientList->id,
                        'status' => 'closed-lead',
                    ])
                    ->each(function ($recipient) use ($campaign, $faker) {
                        //  Attach emails
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 5))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'email'
                            ]);
                        //  Attach text
                        factory(\App\Models\Response::class, $faker->numberBetween(1, 5))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'text'
                            ]);
                        //  Attach phone
                        factory(\App\Models\Response::class, $faker->numberBetween(0, 1))
                            ->create([
                                'campaign_id' => $campaign->id,
                                'recipient_id' => $recipient->id,
                                'type' => 'phone'
                            ]);
                    });


                // Add email drops
                factory(\App\Models\Drop::class, $faker->numberBetween(1,3))
                    ->create([
                        'send_at' => now()->subDays($faker->numberBetween(4,10)),
                        'status' => \App\Models\Drop::STATUS_COMPLETED,
                        'campaign_id' => $campaign->id,
                        'percentage_complete' => 100,
                        'type' => 'email'
                    ])
                    ->each(function( $drop) use ($recipientList) {
                        // Attach all recipients with Emails to this drop
                        $recipientList->recipients()->whereNotNull('email')->chunk(100, function ($recipients) use ($drop) {
                            foreach ($recipients as $recipient) {
                                // Attach the recipient to the drop as successfully sent with a timestamp 1 second staggered
                                $drop->recipients()->save($recipient, ['sent_at' => $drop->send_at->addSeconds($recipient->id)]);
                            }
                        });
                    });

                // Add sms drops
                factory(\App\Models\Drop::class, $faker->numberBetween(1,3))
                    ->create([
                        'send_at' => now()->subDays($faker->numberBetween(4,10)),
                        'status' => \App\Models\Drop::STATUS_COMPLETED,
                        'campaign_id' => $campaign->id,
                        'percentage_complete' => 100,
                        'type' => 'sms'
                    ])
                    ->each(function( $drop) use ($recipientList) {
                        // Attach all recipients with Phones to this drop
                        $recipientList->recipients()->whereNotNull('phone')->chunk(100, function ($recipients) use ($drop) {
                            foreach ($recipients as $recipient) {
                                // Attach the recipient to the drop as successfully sent with a timestamp 1 second staggered
                                $drop->recipients()->save($recipient, ['sent_at' => $drop->send_at->addSeconds($recipient->id)]);
                            }
                        });
                    });
            });
    }

}
