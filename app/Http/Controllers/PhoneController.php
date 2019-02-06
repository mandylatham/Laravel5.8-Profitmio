<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Http\Requests\PhoneProvisionRequest;
use App\Http\Requests\PhoneSearchRequest;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Facades\TwilioClient as Twilio;

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
//        $data = $request->only(['area_code', 'postal_code', 'contains', 'country']);

        $data['country'] = array_key_exists('country', $data) ? $data['country'] : 'US';

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
    }

    public function provision(PhoneProvisionRequest $request)
    {
        try {
            $data = $request->only(['phone_number', 'client_id']);
            $phoneNumber = $data['phone_number'];
            $client_id = $data['client_id'];

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

        $record = new PhoneNumber;
        $record->campaign_id = $request->campaign_id;
        $record->client_id = $client_id;
        $record->phone_number = $phoneNumber;
        $record->call_source_name = $request->call_source_name;
        $record->forward = $request->forward;
        $record->sid = $provision->sid;
        $record->save();

        return $record;
    }

    /**
     * Get just the Phone Numbers for specified Campaign
     *
     * @param \Illuminate\Http\Request       $request
     * @param \App\Http\Controllers\Campaign $campaign
     *
     * @return string
     */
    public function fromCampaign(Request $request, Campaign $campaign)
    {
        $campaign->load('phones');
        $valid_filters = ['phone_number', 'forward'];
        $filters = [];

        foreach ($request->query as $name => $value) {
            if (! empty($value) && in_array($name, $valid_filters)) {
                $filter = [$name, '=', $value];
                array_push($filters, $filter);
            }
        }

        $phones = PhoneNumber::where('campaign_id', $campaign->id)
            ->where($filters);

        if ($request->query->has("sortField") && $request->query->get("sortField") != '') {
            $phones = $phones->orderBy($request->query->get("sortField"), $request->query->get("sortOrder"));
        }

        $count = $phones->count();

        if ($request->has("pageIndex") && $request->has("pageSize")) {
            $toSkip = ($request->query("pageIndex") - 1) * $request->query("pageSize");

            $phones = $phones->skip($toSkip)->take($request->query("pageSize"));

            return json_encode(["itemsCount" => $count, "data" => $phones->get()]);
        }

        return $phones->get()->toJson();
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

    public function edit(Request $request, Campaign $campaign, PhoneNumber $phone)
    {
        $csn = Str::lower($request->input('call_source_name'));
        $csn = in_array($csn, ['mailer', 'email', 'sms']) ? $csn : '';
        $phone->update([
            'forward' => $request->input('forward'),
            'call_source_name' => $csn,
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
