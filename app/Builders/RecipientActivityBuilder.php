<?php
namespace App\Builders;

use App\Models\Drop;
use App\Models\User;
use App\Models\Recipient;
use App\Models\RecipientActivity;

class RecipientActivityBuilder
{
    /**
     * Log marketing event
     *
     * @param Recipient $recipient
     * @param Drop      $drop
     */
    public static function logRecipientMarketed(Recipient $recipient, Drop $drop)
    {
        $metadata = [
            'drop_id' => $drop->id,
            'drop_type' => $drop->type,
        ];

        $lastActivity = $recipient->activity()
                                  ->whereAction(RecipientActivity::MARKETED)
                                  ->orderBy('id', 'desc')
                                  ->first();
        if ($lastActivity) {
            array_push($metadata, $lastActivity->action_at->diffInSeconds(now()));
        }

        $activity = RecipientActivity::create([
            'action' => RecipientActivity::MARKETED,
            'action_at' => now(),
            'user_id' => '0',
            'metadata' => $metadata,
        ]);

        $activity->load('impersonation.impersonator');

        $recipient->activity()->save($activity);
    }

    /**
     * Log open event
     *
     * @param Recipient $recipient
     * @param User      $user
     */
    public static function logOpen(Recipient $recipient, User $user)
    {
        // Get the first recipient response time
        $lastActivityAt = $recipient->responses()->first()->created_at;


        $activity = RecipientActivity::create([
            'action' => RecipientActivity::OPENED,
            'action_at' => now(),
            'user_id' => $user->id,
            'metadata' => [
                'seconds_since_last_activity' => $lastActivityAt->diffInSeconds(now()),
            ]
        ]);

        $activity->load('impersonation.impersonator');

        $recipient->activity()->save($activity);
    }

    /**
     * Log open event
     *
     * @param Recipient $recipient
     * @param User      $user
     */
    public static function logClosed(Recipient $recipient, User $user)
    {
        try {
            $lastInbound = $recipient->getLastInboundDialogStart();
            $lastOutbound = $recipient->getLastOutboundDialogStart();
        } catch (\Exception $e) {
            throw new \Exception("Unable to get dialog data: {$e->getMessage()}");
        }

        $activity = RecipientActivity::create([
            'action' => RecipientActivity::CLOSED,
            'action_at' => now(),
            'user_id' => $user->id,
            'metadata' => [
                'seconds_since_last_inbound' => $lastInbound ? $lastInbound->diffInSeconds(now()) : null,
                'seconds_since_last_outbound' => $lastOutbound ? $lastOutbound->diffInSeconds(now()) : null,
            ]
        ]);

        $activity->load('impersonation.impersonator');

        $recipient->activity()->save($activity);
    }

    /**
     * Log reopen event
     *
     * @param Recipient $recipient
     * @param User      $user
     */
    public static function logReopen(Recipient $recipient, User $user)
    {
        // Get the first recipient response time
        $closed = $recipient->activity()->whereAction(RecipientActivity::CLOSED)->orderBy('id', 'desc')->first();


        $activity = RecipientActivity::create([
            'action' => RecipientActivity::REOPENED,
            'action_at' => now(),
            'user_id' => $user->id,
            'metadata' => [
                'seconds_since_closed' => $closed->action_at->diffInSeconds(now()),
            ]
        ]);

        $activity->load('impersonation.impersonator');

        $recipient->activity()->save($activity);
    }

    /**
     * Log sent sms event
     *
     * @param Recipient $recipient
     * @param User      $user
     *
     * @todo fix metadata
     */
    public static function logSendSms(Recipient $recipient, Response $response, User $user)
    {
        $lastInbound = $recipient->getLastInboundDialogStart();

        // todo fix outbound
        $activity = RecipientActivity::create([
            'action' => RecipientActivity::REOPENED,
            'action_at' => now(),
            'user_id' => $user->id,
            'metadata' => [
                'seconds_since_lead_dialog' => $lastInbound->diffInSeconds(now()),
                'allow_points' => true
            ]
        ]);

        $activity->load('impersonation.impersonator');

        $recipient->activity()->save($activity);
    }
}
