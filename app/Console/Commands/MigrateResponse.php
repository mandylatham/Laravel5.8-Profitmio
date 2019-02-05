<?php

namespace App\Console\Commands;

use App\Models\Response;
use DB;
use Illuminate\Console\Command;

class MigrateResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate response';

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
        $this->info('====== Migrating response table ==============');
        Response::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('responses')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('responses')->orderBy('response_id')->chunk($size, function($responses) use ($bar) {
            $insert = [];
            foreach ($responses as $response) {
                $trans = (array) $response;
                $trans['id'] = $trans['response_id'];
                unset($trans['response_id']);
                $trans['recipient_id'] = $trans['target_id'];
                unset($trans['target_id']);
                $trans['recording_url'] = $trans['recording_uri'];
                unset($trans['recording_uri']);
                $bar->advance();
                $insert[] = $trans;
            }
            Response::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}
