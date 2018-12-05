<?php

namespace App\Console\Commands;

use App\Classes\DropWorker;
use Illuminate\Console\Command;

class SendDrops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drops:send';

    /**
     * Handles all Drop Management
     *
     * @var \App\Classes\DropWorker
     */
    protected $worker;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds all scheduled drops to the send queue';

    /**
     * Create a new command instance.
     *
     * @param \App\Classes\DropWorker $worker
     */
    public function __construct(DropWorker $worker)
    {
        parent::__construct();

        $this->worker = $worker;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (env('APP_ENV') != 'production') {
            $this->error("NOT PRODUCTION ENVIRONMENT. EXITING.");
            return;
        }

        $this->info('Starting the process');

        $this->worker->sendAllDue();

        $this->info('Done');
        return;
    }
}
