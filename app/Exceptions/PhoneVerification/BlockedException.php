<?php

namespace App\Exceptions\PhoneVerification;

class BlockedException extends PhoneVerificationException
{
    protected $message = "Validations are currently blocked for the phone number";
}
