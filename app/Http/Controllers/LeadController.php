<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * @var \App\Classes\MailgunService
     */
    protected $mailgun;

    /**
     * @var App\Services\SentimentService
     */
    protected $sentiment;

    /**
     * Constructor.
     * 
     * @param MailgunService   $mailgun   Dependency Injected Class
     * @param SentimentService $sentiment Dependency Injected Class
     */
    public function __construct(MailgunService $mailgun, SentimentService $sentiment)
    {
        $this->mailgun = $mailgun;
        $this->sentiment = $sentiment;
    }

    public function index(Request $request)
    {
        // Load view
    }

    public function search(Request $request)
    {
        // Apply search if needed
    }

    public function show(Lead $lead)
    {
        // Validate the request

        // Authorize the request

        // Gather object data from models

        // Convert raw data to custom object notation
    }

    public function open(Lead $lead)
    {
        // Sanity check: cuurent state is new

        // Open the Lead

        // Broadcast update to counts
    }

    public function close(Lead $lead)
    {
        // Sanity check: current state is open

        // Close the Lead

        // Broadcast update to counts
    }

    public function reopen(Lead $lead)
    {
        // Sanity check: current state is closed

        // ReOpen the Lead

        // Broadcast update to counts
    }

    public function sendText(Lead $lead, Request $request)
    {
        //
    }

    public function sendEmail(Lead $lead, Request $request)
    {
        //
    }

    public function updateNotes(Lead $lead, Request $request)
    {
        // 
    }

    public function addAppointment(Lead $lead, Request $request)
    {
        //
    }

    public function markCalledBack(Lead $lead, Appointment $callback)
    {
        //
    }

    public function sendToServiceDepartment(Lead $lead)
    {
        //
    }

    public function sendToCrm(Lead $lead)
    {
        //
    }
}
