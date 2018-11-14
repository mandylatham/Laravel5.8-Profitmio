<?php

use Illuminate\Database\Seeder;

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
        $this->call(PhoneNumberSeeder::class);
        $this->call(CampaignTableSeeder::class);
        $this->call(RecipientListTableSeeder::class);
        $this->call(RecipientTableSeeder::class);
        $this->call(AppointmentTableSeeder::class);
    }
}
