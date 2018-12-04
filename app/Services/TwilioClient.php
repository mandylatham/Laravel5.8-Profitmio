<?php
namespace App\Services;

use Twilio\Http\CurlClient;
use Twilio\Rest\Client as Twilio;

class TwilioClient
{
    private $client;

    public function __construct()
    {
        $credentials = [
            'development'=>[
                'accountSid'=>'AC03161de6106ecfe58f6dddba73c74cd3',
                'authToken'=>'1663e705b7ce7d34d3e061322a2c64ac'
            ],
            'production' => [
                'accountSid'=>'AC54df136d44c9dce58f480665c4d22094',
                'authToken'=>'42c8a09653961391287ea54359aba573'
            ]
        ];

        $this->client = new Twilio(
            $credentials['production']['accountSid'],
            $credentials['production']['authToken'],
            $credentials['production']['accountSid'],
            null,
            new CurlClient([CURLOPT_SSL_VERIFYHOST => false])
        );
    }

    public function phoneNumberLookup($data = false)
    {
        if (! is_array($data)) {
            throw new \Exception('Unable to perform a lookup on a null query');
        }

        $data['ExcludeLocalAddressRequired'] = true;
        $data['Voice'] = true;
        $data['SMS'] = true;
        $data['MMS'] = true;

        $data['numbers'] = $this->client->availablePhoneNumbers($data['country'])->local->read($data);

        return $data;
    }

    public function provisionNumber(array $phoneData)
    {
        return $this->client->incomingPhoneNumbers->create($phoneData);
    }

    public function releaseNumber(array $phoneData)
    {
        # This was not a part of the old system
    }

    public function getRecordingFromSid($sid = null)
    {
        $recording = [];

        if ($sid == null) {
            return $recording;
        }

        $call = $this->client->calls($sid)->fetch();
        $callrec = $call->recordings->read();

        if (count($callrec) > 0) {
            $callrec[0]->uri = substr($callrec[0]->uri, 0, -4) . 'mp3';

            $recording = $callrec[0];
        }

        return $recording;
    }


    public function sendSms($from, $to, $message, $image = null)
    {
        $payload = [
            'from' => $from,
            'body' => $message
        ];

        if ($image) {
            $payload['mediaUrl'] = $image;
        }

        return $this->client->messages->create($to, $payload);
    }

    public function getNameFromPhoneNumber($from)
    {
        $sender = $this->client->lookups->phoneNumbers($from)->fetch(['type' => 'caller-name']);

        if ($sender) {
            $nameRaw = explode(' ', $sender->callerName['caller_name']);
            $name = [
                'last_name' => array_pop($nameRaw),
                'first_name' => implode(' ', $nameRaw),
            ];
            ksort($name);
        } else {
            $name = ['first_name' => 'Unknown', 'last_name' => 'Unknown'];
        }

        return $name;
    }

    public function getFormattedPhoneNumber($number)
    {
        try {
            return $this->client
                ->lookups
                ->phoneNumbers($number, ['CountryCode' => 'US'])
                ->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            $number = preg_replace('/[^0-9]/', '', $number);

            return '+1' . $number;
        }
    }
}
