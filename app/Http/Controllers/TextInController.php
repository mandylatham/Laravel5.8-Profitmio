<?php

namespace App\Http\Controllers;

use App\Events\CampaignCountsUpdated;
use App\Models\Appointment;
use App\Models\PhoneNumber;
use App\Models\Recipient;
use App\Models\Response;
use App\Facades\TwilioClient as Twilio;
use Illuminate\Http\Request;

class TextInController extends Controller
{
    private const CALLBACK_MESSAGE = 'Profit Miner callback requested via text-in board for %s at %s';

    public function createFromSms(Request $request)
	{
		if (! $request->has('From') || ! $request->has('To')) {
			return response()->json(['error' => 'missing parameters'], 422);
		}

		try {
			$from = $request->input('From');
			$to   = $request->input('To');
			$phoneNumber = PhoneNumber::where('phone_number', 'like', '%'.$to)->firstOrFail();
			$campaign = $phoneNumber->campaign;
			$recipient = $campaign->recipients()
				->whereRaw("replace(phone, '+1', '') = replace(?, '+1', '')", [$from])
				->first();
			if (! $recipient) {
				$sender = (object)Twilio::getNameFromPhoneNumber($from);
				$recipient = Recipient::create([
					'first_name'  => $sender->first_name ?: 'Unknown',
					'last_name'   => $sender->last_name ?: 'Name',
					'phone'       => $from,
					'email'       => '',
					'campaign_id' => $phoneNumber->campaign_id,
				]);
			}
			$callback = Appointment::create([
				'recipient_id' => $recipient->id,
				'campaign_id'  => $phoneNumber->campaign_id,
				'first_name'   => $recipient->first_name,
				'last_name'    => $recipient->last_name,
				'phone_number' => $recipient->phone,
				'email'        => $recipient->email,
				'auto_year'    => $recipient->year,
				'auto_make'    => $recipient->make,
				'auto_model'   => $recipient->model,
				'type'         => 'callback',
			]);
			$recipient->update(['callback' => 1]);
			$response = Response::create([
				'recipient_id' => $recipient->id,
				'campaign_id'  => $recipient->campaign_id,
				'incoming'     => 1,
				'type'         => 'phone',
				'duration'     => 0,
				'response_source' => $from,
				'response_destination' => $to,
			]);
			event(new CampaignCountsUpdated($campaign));

			if ($campaign->sms_on_callback == 1) {
			try {
				$phone = $campaign->phones()->whereCallSourceName('sms')->orderBy('id', 'desc')->firstOrFail();
				$notify_from = $phone->phone_number;
				$message = $this->getCallbackMessage($callback);
			    $notify_to_numbers = (array)$campaign->sms_on_callback_number;
			    foreach ($notify_to_numbers as $notify_to) {
				Twilio::sendSms($notify_from, $notify_to, $message);
				\Log::debug("Callback notifications sent for text-in message for callback #{$callback->id} to $notify_to");
			    }
				} catch (\Exception $e) {
					\Log::error("Unable to send callback SMS: " . $e->getMessage());
				}
			}
		} catch (\Exception $e) {
			\Log::error("Unable to register callback from text-in: (from:$from,to:$to) " . $e->getMessage());
			abort(500, 'Unable to complete your request');
		}
	}
    private function getCallbackMessage(Appointment $appointment): string
    {
        $name = $appointment->first_name.' '.$appointment->last_name;
        return sprintf(self::CALLBACK_MESSAGE, $name, $appointment->phone_number);
    }
}
