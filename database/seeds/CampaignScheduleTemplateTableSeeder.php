<?php

use App\Models\CampaignScheduleTemplate;
use Illuminate\Database\Seeder;

class CampaignScheduleTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CampaignScheduleTemplate::class, 150)->create();
    }
}
