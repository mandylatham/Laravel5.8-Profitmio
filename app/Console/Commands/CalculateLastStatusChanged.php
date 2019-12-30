<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class CalculateLastStatusChanged extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:calculate-last-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the last_status_changed_at value for all leads in the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Add initial value for this computed column
        Lead::with('responses')->chunk(100, function ($leads) {
            $this->info("processing next " . count($leads) . " leads");
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
