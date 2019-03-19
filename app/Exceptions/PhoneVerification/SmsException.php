<?php

namespace App\Exceptions\PhoneVerification;

class SmsException extends PhoneVerificationException
{
    protected $message = "Unable to send an SMS to the phone number";
}
