<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Services\FacebookService;
use App\Models\GlobalSettings;
/**
 * Created by PhpStorm.
 * User: brett
 * Date: 6/1/17
 * Time: 1:39 PM
 */

function result_array($result)
{
    $output = [];

    foreach ($result as $row) {
        $output[] = (array)$row;
    }

    return $output;
}

function result_array_values($result)
{
    $output = [];

    foreach ($result as $row) {
        $array = (array)$row;
        $values = array_values($array);
        $output[] = $values[0];
    }

    return $output;
}

function show_date($date, $format = 'm/d/Y g:i A T')
{
    if (!($date instanceof Carbon)) {
        if (is_numeric($date)) {
            $date = Carbon::createFromTimestamp($date);
        } else {
            $date = Carbon::parse($date);
        }
    }

    return $date->setTimezone(Auth::user()->timezone)->format($format);
}

function getDateWithTimezone($date, $timezone, $format = 'm/d/Y g:i A T')
{
    if (!isValidTimezoneId($timezone)) {
        Log::error("helpers@getDateWithTimezone(): invalid timezone provided, {$timezone}");

        return $date;
    }

    if (!($date instanceof Carbon)) {
        if (is_numeric($date)) {
            $date = Carbon::createFromTimestamp($date);
        } else {
            $date = Carbon::parse($date);
        }
    }

    return $date->setTimezone($timezone)->format($format);
}

function isValidTimezoneId($timezoneId)
{
    try {
        new DateTimeZone($timezoneId);
    } catch (Exception $e) {
        return false;
    }

    return true;
}

function get_active_company()
{
    return session()->get('activeCompany');
}

function get_active_company_model()
{
    return \App\Models\Company::findOrFail(session()->get('activeCompany'));
}

function get_times($default = '19:00', $interval = '+30 minutes', $firstElement = '')
{
    $output = '';
    if (!empty($firstElement)) {
        $output .= $firstElement;
    }

    $current = strtotime('00:00');
    $end = strtotime('23:59');

    while ($current <= $end) {
        $time = date('H:i', $current);
        $sel = ($time == $default) ? ' selected' : '';

        $output .= "<option value=\"{$time}\"{$sel}>" . date('h.i A', $current) . '</option>';
        $current = strtotime($interval, $current);
    }

    return $output;
}

function getNotifications()
{
    $globalSettings = GlobalSettings::where('name', 'facebook_access_token')->first() ?? (object) ['value' => null];
    $access_token = $globalSettings->value;
    if(empty($access_token)){
        return [
            "settings" => [
                (object) [
                    "level" => "warning",
                    "title" => "Connect your Facebook account",
                    "description" => "The credentials for accessing the Facebook API are not configurated."
                ]
            ]
        ];
    }

    $facebookService = new FacebookService();

    if(!$facebookService->isValidAccessToken($access_token)){
        return [
            "settings" => [
                (object) [
                    "level" => "warning",
                    "title" => "Facebook access credentials expired",
                    "description" => "The credentials for accessing the Facebook API have expired or are no longer valid. It is necessary to renew them manually by reconnecting your account."
                ]
            ]
        ];
    }

    return [];
}
