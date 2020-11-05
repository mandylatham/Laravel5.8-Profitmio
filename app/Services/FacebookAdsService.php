<?php
namespace App\Services;

use Exception;

// Facebook Bussiness SDk
use FacebookAds\Api;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Object\Campaign as FacebookCampaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;

class FacebookAdsService
{
    /**
     * FacebookAdsService constructor.
     */
    public function __construct($access_token)
    {
        if (empty($access_token)) {
            throw new Exception('Unable to connect with facebook, access_token required.');
        }

        $credentials = [
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'access_token' => $access_token
        ];

        Api::init(
            $credentials['app_id'],
            $credentials['app_secret'],
            $credentials['access_token']
        );
    }

    public function getCampaignMetrics($campaignId)
    {
        $campaign = new FacebookCampaign($campaignId);

        $summary = $campaign->getInsights(
            [
                'actions',
                'reach',
                'impressions'
            ],
            [
                'date_preset' => AdsInsightsDatePresetValues::LIFETIME,
                'breakdowns' => [
                    'gender'
                ],
                'summary' => [
                    'reach',
                    'actions',
                    'frequency',
                    'impressions'
                ]
            ]
        );

        $performance = $campaign->getInsights(
            [
                'actions',
                'reach',
                'frequency'
            ],
            [
                'date_preset' => AdsInsightsDatePresetValues::LIFETIME,
                'time_increment' => 1
            ]
        );

        $demographics = $campaign->getInsights(
            [
                'reach',
                'actions',
                'impressions'
            ],
            [
                'date_preset' => AdsInsightsDatePresetValues::LIFETIME,
                'breakdowns' => [
                    'age',
                    'gender'
                ]
            ]
        );

        $metrics = [
            'campaign_id' => $campaignId,
            'campaign_name' => 'Not found',
            'summary' => $summary->getResponse()->getContent()['summary'],
            'performance' => $this->fetchAllData($performance),
            'demographics' => $this->fetchAllData($demographics),
            'demographics_summary' => $this->fetchAllData($summary),
        ];

        return $metrics;
    }

    public function hasAccessToCampaign($campaignId)
    {
        $campaign = new FacebookCampaign($campaignId);

        try {
            $campaign->getSelf([
                CampaignFields::ID
            ]);
        } catch (AuthorizationException $exception) {
            return false;
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return true;
    }

    private function fetchAllData($metric)
    {
        $allData = [];
        do {
            $data = $metric->getResponse()->getContent()['data'];
            $allData = array_merge($allData, $data);
            $metric->fetchAfter();
        } while ($metric->getNext());

        return $allData;
    }
}
