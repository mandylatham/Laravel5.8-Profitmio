<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\TwilioClient as Twilio;

class TwilioClient extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'TwilioClient'; // the IoC binding.
    }
}
