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
    protected $signature = 'pm-import:response';

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
        $this->info('Responses migration started.');
        Response::truncate();
        DB::insert('insert into profitminer.responses
(id, campaign_id, recipient_id, type, message, call_sid, recording_sid, call_phone_number_id, response_source, response_destination, recording_url, duration, message_id, in_reply_to, subject, incoming, created_at, updated_at, deleted_at)
select response_id, campaign_id, target_id, type, message, call_sid, recording_sid, call_phone_number_id, response_source, response_destination, recording_uri, duration, message_id, in_reply_to, subject, incoming, created_at, updated_at, deleted_at from profitminer_original_schema.responses;');
        $this->info('Responses migration completed.');
    }
}
