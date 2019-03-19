<?php

namespace App\Exceptions\PhoneVerification;

class AlreadyStartedException extends PhoneVerificationException
{
    protected $message = "The phone number has already been sent a code";
}
