<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncomingMessageController extends Controller
{
    public function receiveSmsMessage(Request $request)
    {
        // Add the record
    }

    public function receiveEmailMessage(Request $request)
    {
        // Add the record
    }

    public function receivePhoneCall(Request $request)
    {
        // Add it.
    }

    public function receivePhoneCallStatus(Request $request)
    {
        // Do it.
    }

    public function getEmailMetadata(Request $request)
    {
        // separate email into object references
    }
}
