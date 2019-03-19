<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\PhoneNumber;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Facades\TwilioClient as Twilio;
use App\Http\Requests\PhoneSearchRequest;
use App\Http\Requests\PhoneProvisionRequest;

class PhoneController extends Controller
{
    /**
     * Search all available phone numbers
     *
     * @param \App\Http\Requests\PhoneSearchRequest $request
     *
     * @return array
     */
    public function searchAvailable(PhoneSearchRequest $request)
    {
        $data = $request->only(['areaCode', 'inPostalCode', 'contains', 'country']);
        $data['country'] = array_key_exists('country', $data) ? $data['country'] : 'US';
        try {
            $twilioNumbers = Twilio::phoneNumberLookup($data);
            $numbers = [];
            foreach ($twilioNumbers['numbers'] as $number) {
                $num['phone'] = $number->friendlyName;
                $num['phoneNumber'] = $number->phoneNumber;
                $num['location'] = trim($number->rateCenter . ' ' . $number->region .  ' ' . $number->isoCountry);
                $num['zip'] = $number->postalCode;

                $numbers[] = $num;
            }
            $data['numbers'] = $numbers;

            return response()->json($data);
        } catch (\Exception $e) {
            return response(["error" => "Unable to perform phone number operation"], 503);
        }
    }

    public function provision(PhoneProvisionRequest $request)
    {
        try {
            $phoneNumber = $request->input(['phone_number']);

            if (! preg_match('/^[\+]?1[0-9]{10}$/', $phoneNumber)) {
                throw new \Exception("Invalid phone number");
            }

            $phone = [
                'ExcludeLocalAddressRequired' => true,
                'Voice' => true,
                'SMS' => true,
                'MMS' => true,
                'phoneNumber' => $phoneNumber,
                'StatusCallback' => route('pub-api.phone-response-status'),
                'StatusCallbackMethod' => 'post',
                'VoiceUrl' => route('pub-api.phone-response-inbound'),
                'VoiceMethod' => 'post',
                'SmsUrl' => route('pub-api.text-response-inbound'),
                'SmsMethod' => 'post',
            ];

            $provision = Twilio::provisionNumber($phone);
        } catch (\Twilio\Exceptions\RestException $e) {
            $ex = $e->getTrace();
            $message = $ex[0]['args'][0]->getContent();
            echo json_encode(['error'=>1, 'message'=>$message['message']]);
            return;
        }

        $phone = PhoneNumber::create([
            'campaign_id' => $request->campaign_id,
            'phone_number' => $phoneNumber,
            'call_source_name' => $request->call_source_name,
            'forward' => $request->forward,
            'sid' => $provision->sid,
        ]);

        return $phone;
    }

    /**
     * Get just the Phone Numbers for specified Campaign
     *
     * @param \Illuminate\Http\Request       $request
     * @param \App\Http\Controllers\Campaign $campaign
     *
     * @return string
     */
    public function forCampaign(Request $request, Campaign $campaign)
    {
        $campaign->load('phones');

        return $campaign->phones;
    }

    /**
     * Get all campaign phones as Json string
     *
     * @param Campaign $campaign
     * @return void
     */
    public function fromCampaignAsJson(Campaign $campaign)
    {
        return $campaign->phones->toJson();
    }

    public function store(Request $request, Campaign $campaign, $phone)
    {
        $phone = PhoneNumber::findOrFail($phone);
        $source = Str::lower($request->input('call_source_name'));
        $source = in_array($source, array_keys(PhoneNumber::$callSources)) ? $source : '';
        $phone->update([
            'forward' => $request->input('forward'),
            'call_source_name' => $source,
        ]);
        return $phone->fresh();
    }

    public function release(Campaign $campaign, PhoneNumber $phone)
    {
        if (Twilio::releaseNumber($phone->sid)) {
            $phone->delete();
        }

        return $phone;
    }
}
