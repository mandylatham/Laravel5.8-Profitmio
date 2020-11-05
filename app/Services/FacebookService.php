<?php
namespace App\Services;

use Exception;
use Facebook\Facebook;

class FacebookService
{
    private $facebook;

    private $credentials;
    /**
     * FacebookService constructor.
     */
    public function __construct()
    {
        $this->credentials = [
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => env('FACEBOOK_GRAPH_VERSION', 'v8.0')
        ];

        $this->facebook = new Facebook([
            $this->credentials['app_id'],
            $this->credentials['app_secret'],
            $this->credentials['default_graph_version']
        ]);
    }

    public function getAppId()
    {
        return $this->credentials['app_id'];
    }

    public function getAppSecret()
    {
        return $this->credentials['app_secret'];
    }

    public function getGraphVersion()
    {
        return $this->credentials['default_graph_version'];
    }

    public function getLoginUrl($permissions = [])
    {
        $helper = $this->facebook->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl(env('APP_URL').'/settings', $permissions);
        return $loginUrl;
    }

    public function isValidAccessToken($access_token)
    {
        if(empty($access_token)){
            return false;
        }

        $oauth = $this->facebook->getOAuth2Client();
        $meta = $oauth->debugToken($access_token);
        return $meta->getIsValid();
    }

    public function getTokenExpiresAt($access_token)
    {
        if(empty($access_token)){
            return false;
        }

        $oauth = $this->facebook->getOAuth2Client();
        $meta = $oauth->debugToken($access_token);
        return $meta->getExpiresAt();
    }

    public function getExtendedAccessToken($access_token)
    {
        $oauth = $this->facebook->getOAuth2Client();
        $accessTokenLong = $oauth->getLongLivedAccessToken($access_token);
        return $accessTokenLong;
    }
}
