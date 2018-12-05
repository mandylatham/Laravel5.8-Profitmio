<?php

namespace App\Jobs;

use App\Builders\RecipientBuilder;
use App\Models\RecipientList;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LoadListRecipients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $list;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RecipientList $list)
    {
        $this->list = $list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug("ProcessNewRecipientList: creating new recpients for list");
        try {
            $builder = new RecipientBuilder();
            $builder->createFromNewList($this->list);
            ProcessList::dispatch($this->list)->onQueue('lists');
        } catch (\Exception $e) {
            \Log::error("Unable to add recipients for list {$this->list->id}, there was an exception: " . $e->getMessage());
            try {
                $list = RecipientList::findOrFail($this->list->id);
                $list->update([
                    'failed_at' => \Carbon\Carbon::now('UTC'),
                    'failed_reason' => $e->getMessage(),
                ]);
                \Log::debug("Failed recipient list updated with failed reason");
            } catch (\Exception $updateException) {
                \Log::error("Unable to update the list with failed status due to an error: " . $updateException->getMessage());
            }
        }
    }
}
