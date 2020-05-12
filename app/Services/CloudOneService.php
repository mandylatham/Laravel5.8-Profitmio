<?php

namespace App\Services;

use GuzzleHttp\Client;

class CloudOneService
{
    private const BASE_URL = 'https://api.cloudone.com/v3/400344/';

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => CloudOneService::BASE_URL
        ]);
    }

    public function getCampaignPhoneNumber($campaignId)
    {
        $response = $this->client->request('post', 'phone/list.json', [
            'json' => [
                'client_id' => env('CLOUDONE_CLIENT_ID'),
                'api_key' => env('CLOUDONE_API_KEY'),
                'campaign_id' => $campaignId
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $response = json_decode((string) $response->getBody());
            if (isset($response->message) && $response->message === 'Campaign not found') {
                abort(404, 'Invalid CloudOne Campaign ID');
            }
            if (count($response->phone_numbers->phone) > 0) {
                return $response->phone_numbers->phone[0]->phone_number;
            }
        }
        return null;
    }


}
