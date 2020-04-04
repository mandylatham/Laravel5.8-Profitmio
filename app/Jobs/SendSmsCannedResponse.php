<?php

namespace App\Jobs;

use App\Models\CannedResponse;
use App\Models\PhoneNumber;
use App\Models\Recipient;
use App\Models\Response;
use App\Services\TwilioClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmsCannedResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cannedResponse;

    private $phoneNumber;

    private $recipient;

    public function __construct(CannedResponse $cannedResponse, PhoneNumber $phoneNumber, Recipient $recipient)
    {
        $this->cannedResponse = $cannedResponse;
        $this->phoneNumber = $phoneNumber;
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $twig = new \Twig\Environment(
            new \Twig\Loader\ArrayLoader([
                'text' => $this->cannedResponse->response,
            ])
        );
        $message = $twig->render('text', array_diff_key($this->recipient->toArray(), ['pivot' => null]));
        $twilioClient = new TwilioClient();
        $twilioClient->sendSms($this->phoneNumber->phone_number, $this->recipient->phone, $message);

        $this->recipient->responses()->create([
            'message' => $this->cannedResponse->response,
            'incoming' => 0,
            'type' => Response::SMS_TYPE,
            'recording_sid' => 0,
            'campaign_id' => $this->recipient->campaign->id,
            'recipient_id' => $this->recipient->id,
        ]);
    }
}
