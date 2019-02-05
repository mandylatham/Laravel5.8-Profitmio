<?php

namespace App\Console\Commands;

use App\Models\Recipient;
use DB;
use Illuminate\Console\Command;

class MigrateRecipient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:recipient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate recipients';

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
        $this->info('====== Migrating recipient table ==============');
        Recipient::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('targets')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('targets')->orderBy('target_id')->chunk($size, function($recipients) use ($bar) {
            $insert = [];
            foreach ($recipients as $recipient) {
                $trans = (array) $recipient;
                $trans['id'] = $trans['target_id'];
                unset($trans['target_id']);
                $bar->advance();
                $insert[] = $trans;
            }
            Recipient::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}
