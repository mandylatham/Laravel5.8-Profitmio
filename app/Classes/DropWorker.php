<?php namespace App\Classes;

use App\Models\Drop;
use App\Models\EmailLog;
use Carbon\Carbon;
use Log;

/**
 * Class DropWorker
 *
 * Provides information about Drops to facilitate Drop Processing
 */
class DropWorker
{
    /**
     * @var \App\Classes\MailgunService
     */
    protected $mailgun;

    /**
     * DropWorker constructor.
     *
     * @param \App\Classes\MailgunService $mailgun
     */
    public function __construct(MailgunService $mailgun)
    {
        $this->mailgun = $mailgun;
    }

    /**
     * All Drops scheduled to send in specified minutes
     *
     * @param int $minutes
     *
     * @return mixed
     */
    public function getDropsDueSoon($minutes = 5)
    {
        return Drop::emailDueInMinutes($minutes)
            ->whereNull('notified_at')
	    ->with(['campaign' => function ($q) { $q->whereRaw("expires_at >= current_timestamp"); }])
	    ->has('campaign')
            ->with(['campaign.dealership', 'recipients'])
            ->orderBy('send_at')
            ->get();
    }

    /**
     * Drops which are currently due
     *
     * @return mixed
     */
    public function getDropsDue()
    {
        return Drop::emailDue()
            ->with(['campaign', 'campaign.dealership', 'recipients'])
            ->orderBy('send_at')
            ->get();
    }

    /**
     * Send out all drops due
     */
    public function sendAllDue()
    {
        $drops = $this->getDropsDue();

        foreach ($drops as $drop) {
            $this->reserveDrop($drop);
        }
    }

    /**
     * Only run Pending drops
     * @param \App\Drop $drop
     */
    private function reserveDrop(Drop $drop)
    {
        $reserved = Drop::where('id', $drop->id)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Processing',
                'started_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

        if ($reserved == 1) {
            \Log::debug("DropWorker: reserved drop #{$drop->id} for processing");

            return $this->processDrop($drop);
        }

        $debugDrop = collect(Drop::where('id', 1555)->first())
            ->only(['id', 'status', 'created_at', 'send_at', 'started_at', 'completed_at', 'deleted_at'])
            ->toJson();

        \Log::debug("DropWorker: cannot reserve drop#{$drop->id} (count was {$reserved}), skipping");
        \Log::debug("DropWorker: failure to reserve drop#{$drop->id} has current state: {$debugDrop}");
    }

    /**
     * Send the emails
     *
     * @param \App\Drop $drop
     */
    private function processDrop(Drop $drop)
    {
        $loader = new \Twig_Loader_Array([
            'subject' => $drop->email_subject,
            'html' => $drop->email_html,
            'text' => $drop->email_text,
        ]);

	try {
        $twig = new \Twig_Environment($loader);
        \Log::debug("DropWorker: loaded Twig environment for drop #{$drop->id}");
	} catch (\Exception $e) {
	    $this->abortRun($drop, $e, "DropWorker: unable to render email template for drop #{$drop->id}!  Aborting! \nException: {$e->getMessage()}");
	}

        # Determine if passthrough is enabled
        $passthrough = null;
        if ($drop->campaign->hasPassthrough) {
            $passthrough = (array)$drop->campaign->client_passthrough_email;
        }

        # Iterate through recipients and send emails
        $count = 0;
        $errors = 0;
        foreach ($drop->recipients as $recipient) {
            if (empty($recipient->email) or $recipient->last_responsed_at != null or
		($recipient->recipient_list_id > 0  && $recipient->email_valid != 1)
            ) {
                continue;
            }

            $templateVars = collect($recipient->toArray())->except(['pivot'])->toArray();
            try {
                $subject = $twig->render('subject', $templateVars);
                $html = $twig->render('html', $templateVars);
                $text = $twig->render('text', $templateVars);
            } catch (\Exception $e) {
                /*  Mark DropRecipient as Sent  */
                $affected = \DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->update(['failed_at' => Carbon::now()]);

		continue;
            }


            try {
                $reply = $this->mailgun->sendEmail(
                    $drop->campaign,
                    $recipient,
                    $subject,
                    $html,
                    $text
                );

                if (!$reply) {
                    throw new \Exception("mailgun call failed - $reply");
                }
            } catch (\Exception $e) {
                \Log::error("DropWorker: email transport failure on recipient #{$recipient->id} \nException: {$e->getMessage()}");
                $errors++;
                continue;
            }

            try {
                $log = new EmailLog([
                    'message_id' => str_replace(['<', '>'], '', $reply->getId()),
                    'code' => 0,
                    'campaign_id' => $drop->campaign->id,
                    'recipient_id' => $recipient->id,
                    'event' => 'sent',
                    'recipient' => $recipient->email,
                ]);

                if (!$log->save()) {
                    $this->abortRun($drop, $e, "DropWorker: email tracking failure. current recipient is #{$recipient->id} \nAborting! \nException: {$e->getMessage}");
                }
            } catch (\Exception $e) {
                $this->abortRun($drop, $e, "DropWorker: email tracking failure. current recipient is #{$recipient->id} \nAborting! \nException: {$e->getMessage}");
            }

            /*  Mark DropRecipient as Sent  */
            $affected = \DB::table('deployment_recipients')
                ->where('deployment_id', $drop->id)
                ->where('recipient_id', $recipient->id)
                ->update(['sent_at' => Carbon::now()]);

            if ($affected != 1) {
                \Log::error("DropWorker: unable to update sent_at time for drop recipient #{$recipient->id}!");
                $errors++;
            }

            $count++;

            $drop->percentage_complete = floor($count / $drop->recipients->count());
        }

        \Log::debug("DropWorker: drop #{$drop->id} completed processing with {$errors} errors");
        $drop->status = 'Completed';
        $drop->completed_at = Carbon::now();
        $drop->percentage_complete = 100;
        $drop->save();
    }

    private function abortRun(Drop $drop, \Exception $e, $message)
    {
        \Log::critical($message);

        \DB::table('campaign_schedules')
            ->where('id', $drop->id)
            ->update(['status' => 'Aborted']);

        dd($e);
    }
}
