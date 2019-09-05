<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Response;
use App\Models\Recipient;
use Illuminate\Http\Request;
use App\Events\CampaignCountsUpdated;
use App\Events\RecipientEmailResponseReceived;

class IncomingMessageController extends Controller
{
    public function receiveSmsMessage(Request $request)
    {
        // Add the record
    }

    public function receiveEmailMessage(Request $request)
    {
        list($campaign_id, $recipient_id) = $this->getEmailMetadata($request->get('To'));

        $campaign = Campaign::findOrFail($campaign_id);
        $recipient = Recipient::findOrFail($recipient_id);

        $response = new Response([
            'campaign_id'   => $campaign->id,
            'recipient_id'  => $recipient->id,
            'message'       => $request->get('stripped-text'),
            'message_id'    => $request->get('Message-Id'),
            'in_reply_to'   => $request->get('In-Reply-To'),
            'subject'       => $request->get('subject'),
            'type'          => 'email',
            'recording_sid' => 0,
            'incoming'      => 1,
        ]);

        $response->save();

        $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
        $recipient->save();

        $this->sentiment->forResponse($response);

        event(new RecipientEmailResponseReceived($campaign, $recipient, $response));
        event(new CampaignCountsUpdated($campaign));

        if ($campaign->client_passthrough && !empty($campaign->client_passthrough_email)) {
            $this->mailgun->sendPassthroughEmail(
                $campaign,
                $recipient,
                $request->get('subject'),
                $request->get('stripped-html'),
                $request->get('stripped-text')
            );
        }
    }

    public function receivePhoneCall(Request $request)
    {
        // Add it.
    }

    public function receivePhoneCallStatus(Request $request)
    {
        // Do it.
    }

    /**
     * Parse out the encoded email data
     *
     * @param $email
     *
     * @return array
     */
    protected function getEmailMetadata($email) : array
    {
        $metadata = preg_split('/(_|@)/', $email);

        $campaign_id = $metadata[1];
        $recipient_id = $metadata[2];

        return [$campaign_id, $recipient_id];
    }
}
