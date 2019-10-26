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

        Artisan::call('leads:calculate-last-status');
    }

    /**
     * Perform some manual operations to keep the console sane
     *
     * @return void
     */
    private function setupResponsesForConsole()
    {
        App\Models\Lead::with('responses')->chunk(100, function ($leads) {
            foreach ($leads as $lead) {
                $lsca = $this->calculateInitialValue($lead->responses);
                $lead->update(['last_status_changed_at' => $lsca]);
            }
        });
    }

    /**
     * Calculates the initial value for last_status_changed_at
     *
     * @param Collection $responses
     *
     * @return string
     */
    private function calculateInitialValue($responses)
    {
        $lsca = $responses->first()->created_at;

        for ($i=0; $i<count($responses); $i++) {
            if ($i < 1) { continue; }
            if ($responses[$i]->incoming == 1 && $responses[$i-1]->incoming == 0) {
                $lsca = $responses[$i]->created_at;
            }
        }

        return $lsca;
    }
}
