<?php
namespace App\Services;

use Exception;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client as Twilio;

class TwilioClient
{
    private $client;

    /**
     * TwilioClient constructor.
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct()
    {
        $credentials = [
            'accountSid' => env('TWILIO_ACCOUNT_SID'),
            'authToken' => env('TWILIO_AUTHTOKEN'),
        ];

        $this->client = new Twilio(
            $credentials['accountSid'],
            $credentials['authToken'],
            $credentials['accountSid'],
            null,
            new CurlClient([CURLOPT_SSL_VERIFYHOST => false])
        );
    }

    /**
     * @param bool $data
     * @return array|bool
     * @throws Exception
     */
    public function phoneNumberLookup($data = false)
    {
        if (! is_array($data)) {
            throw new Exception('Unable to perform a lookup on a null query');
        }

        $data['ExcludeLocalAddressRequired'] = true;
        $data['Voice'] = true;
        $data['SMS'] = true;
        $data['MMS'] = true;

        $data['numbers'] = $this->client->availablePhoneNumbers($data['country'])->local->read($data);

        return $data;
    }

    /**
     * @param array $phoneData
     * @return \Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberInstance
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function provisionNumber(array $phoneData)
    {
        return $this->client->incomingPhoneNumbers->create($phoneData);
    }

    /**
     * @param $phoneSid
     * @return bool
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function releaseNumber($phoneSid)
    {
        return $this->client->incomingPhoneNumbers($phoneSid)->delete();
    }

    /**
     * @param null $sid
     * @return array
     * @throws \Twilio\Exceptions\TwilioException
     */
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


    /**
     * @param      $from
     * @param      $to
     * @param      $message
     * @param null $image
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
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

    /**
     * @param $from
     * @return array
     */
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

    /**
     * @param $number
     * @return string|\Twilio\Rest\Lookups\V1\PhoneNumberInstance
     */
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
