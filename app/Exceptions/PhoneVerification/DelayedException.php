<?php

namespace App\Exceptions\PhoneVerification;

class DelayedException extends PhoneVerificationException
{
    protected $message = "Verifications are delayed for this phone number";
}
