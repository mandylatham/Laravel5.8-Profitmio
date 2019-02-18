<?php

namespace App\Console\Commands;

use Mail;
use App\Models\Drop;
use \App\Classes\DropWorker;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDropNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drops:notify';

    /**
     * The worker for all Drop Management
     *
     * @var \App\Console\Commands\DropWorker
     */
    protected $worker;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email clients who have an upcoming drop';

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

        $this->info('Running Notifications');

        $drops = $this->worker->getDropsDueSoon(5);

        if ($drops->count() == 0) {
            $this->info('No drops are scheduled to send in the next 5 minutes.');
            return;
        }

        foreach ($drops as $drop) {
            if (Drop::where('id', $drop->id)->whereNull('notified_at')->get()) {
                $this->info("Sending notification for drop #{$drop->id} under campaign #{$drop->campaign->id}");
                $drop->notified_at = Carbon::now();

                $drop->save();

                if ($drop->campaign->lead_alerts) {
                    foreach ((array)$drop->campaign->lead_alert_email as $email) {
                        $email = trim($email);
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            \Log::error("SendDropNotifications@handle (line 74): Skipping drop notification for invalid email, $email");
                        }

                        Mail::send('emails.drop-notification', ['drop' => $drop], function ($message) use ($drop, $email) {
                            $message->subject("Your Profit Miner campaign is going out now")
                                ->to($email, $drop->campaign->client->name)
                                ->from('no-reply@alerts.profitminer.io');
                        });
                    }
                }
            }
        }

        $this->info('Done');
    }
}
