<?php
namespace App\Classes;

use App\Models\Campaign;
use App\Models\Response;
use App\Models\Recipient;
use Illuminate\Http\Request;

class InboundMessageHandler
{
    protected $smsEngine;
    protected $emailEngine;

    public function __construct(MailgunService $emailEngine, TwilioClient $smsEngine)
    {
        $this->emailEngine = $emailEngine;
        $this->smsEngine = $smsEngine;
    }

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
        // Send it.
    }

    public function getEmailMetadata(Request $request)
    {
        // separate email into object references
    }
}
