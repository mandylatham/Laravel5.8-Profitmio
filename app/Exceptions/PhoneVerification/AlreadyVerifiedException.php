<?php

namespace App\Exceptions\PhoneVerification;

class AlreadyVerifiedException extends PhoneVerificationException
{
    protected $message = "Phone number is already verified";
}
