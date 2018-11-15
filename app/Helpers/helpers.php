<?php
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
        $output[] = (array) $row;
    }

    return $output;
}

function result_array_values($result)
{
    $output = [];

    foreach ($result as $row) {
        $array = (array) $row;
        $values = array_values($array);
        $output[] = $values[0];
    }

    return $output;
}

function show_date($date, $format = 'm/d/Y g:i A T')
{
    if (!($date instanceof \Carbon\Carbon)) {
        if (is_numeric($date)) {
            $date = \Carbon\Carbon::createFromTimestamp($date);
        } else {
            $date = \Carbon\Carbon::parse($date);
        }
    }

    return $date->setTimezone(Auth::user()->timezone)->format($format);
}

function getDateWithTimezone($date, $timezone, $format = 'm/d/Y g:i A T')
{
    if (!isValidTimezoneId($timezone)) {
        \Log::error("helpers@getDateWithTimezone(): invalid timezone provided, {$timezone}");

        return $date;
    }

    if (!($date instanceof \Carbon\Carbon)) {
        if (is_numeric($date)) {
            $date = \Carbon\Carbon::createFromTimestamp($date);
        } else {
            $date = \Carbon\Carbon::parse($date);
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
