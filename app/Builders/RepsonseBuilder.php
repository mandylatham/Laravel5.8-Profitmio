<?php
namespace App\Builders;

use App\Models\Lead;
use App\Models\User;
use App\Models\EmailLog;
use App\Models\Response;

class ResponseBuilder
{
    /**
     * Build the SMS Reply Objects
     *
     * @param User $user
     * @param Lead $lead
     * @param string $message
     * @param string $sid
     *
     * @return Response
     */
    public static function buildSmsReply(User $user, Lead $lead, $message, $sid) : Response
    {
        // Mark all previous messages as read
        Response::where('type', 'text')
            ->where('campaign_id', $lead->campaign->id)
            ->where('recipient_id', $lead->id)
            ->update(['read' => true]);

        $response = Response::create([
            'campaign_id'   => $lead->campaign->id,
            'recipient_id'  => $lead->id,
            'message'       => $message,
            'message_id'    => $sid,
            'incoming'      => 0,
            'read'          => 1,
            'type'          => 'text',
            'user_id'       => $user->id,
            'recording_sid' => 0,
        ]);

        $response->load('impersonation.impersonator');

        return $response;
    }

    /**
     * Build the Email Reply Objects
     *
     * @param User $user
     * @param Lead $lead
     * @param string $message
     * @param string $sid
     *
     * @return Response
     */
    public static function buildEmailReply(User $user, Lead $lead, $lastMessageId, $subject, $message, $sid) : Response
    {
        // Mark all previous messages as read
        Response::where('type', 'email')
            ->where('campaign_id', $lead->campaign->id)
            ->where('recipient_id', $lead->id)
            ->update(['read' => true]);

        # Save the response
        $response = Response::create([
            'campaign_id'   => $lead->campaign->id,
            'recipient_id'  => $lead->id,
            'message'       => $message,
            'message_id'    => $sid,
            'in_reply_to'   => $lastMessageId,
            'subject'       => $subject,
            'incoming'      => 0,
            'type'          => 'email',
            'recording_sid' => 0,
            'duration'      => 0,
            'user_id'       => $user->id,
        ]);

        $response->load('impersonation.impersonator');

        # Log the transaction
        $log = EmailLog::create([
            'message_id'   => str_replace(['<', '>'], '', $sid),
            'code'         => 0,
            'campaign_id'  => $lead->campaign->id,
            'recipient_id' => $lead->id,
            'event'        => 'reply',
            'recipient'    => $lead->email,
        ]);

        return $response;
    }
}