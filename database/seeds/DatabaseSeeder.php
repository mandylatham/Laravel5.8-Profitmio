<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanyTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CampaignTableSeeder::class);
        $this->call(LeadTagSeeder::class);
        $this->call(GlobalSettingsSeeder::class);
    }
}
