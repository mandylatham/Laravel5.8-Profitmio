<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Http\Requests\PhoneProvisionRequest;
use App\Http\Requests\PhoneSearchRequest;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Twilio;

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
                'StatusCallback' => secure_url('/phone-responses/status'),
                'StatusCallbackMethod' => 'post',
                'VoiceUrl' => secure_url('/phone-responses/inbound'),
                'VoiceMethod' => 'post',
                'SmsUrl' => secure_url('/text-responses/inbound'),
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
        $record->client_id = $client_id;
        $record->phone_number = $phoneNumber;
        $record->sid = $provision->sid;
        $record->save();

        return json_encode(['id' => $record->phone_number_id, 'number' => $record->phone_number]);
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
}
