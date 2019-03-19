<?php

namespace App\Exceptions\PhoneVerification;

class InvalidConfigurationException extends PhoneVerificationException
{
    protected $message = "The verification service has a problem";
}
