<?php
namespace App\Classes;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Http\Request;

class LeadManager
{
    protected $campaign;

    /**
     * Constructor.
     * 
     * @param Campaign $campaign The Campaign.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Search the campaign for leads
     * 
     * @param Request $request The search Request.
     */
    public function searchLeads(Request $request)
    {
        // do search
    }

    /**
     * Get Lead Stats for the Campaign
     */
    public function getCampaignLeadStats()
    {
        // return campaign stats
    }

    /**
     * Get the Lead out
     * 
     * @param Recipient $recipient Get in the choppa.
     */
    public function getLead(Recipient $recipient)
    {
        // get that lead
    }

    /**
     * Open the Lead
     * 
     * @param Recipient $recipient The lead to open.
     */
    public function openLead(Recipient $recipient)
    {
        // open the dude
    }

    /**
     * Close the Lead
     * 
     * @param Recipient $recipient The lead to close.
     */
    public function closeLead(Recipient $recipient)
    {
        // close that dude
    }

    /**
     * Reopen that Lead
     * 
     * @param Recipient $recipient What did you forget?
     */
    public function reopenLead(Recipient $recipient)
    {
        // reopen that thang
    }

    /**
     * Add an Appointment
     * 
     * @param Recipient $recipient Who dat?
     * @param Appointment $appointment That thing.
     */
    public function addAppointment(Recipient $recipient, Request $request)
    {
        // do the things
    }

    /**
     * Send an email reply to the Lead
     * 
     * @param Recipient $recipient That dude.
     * @param Request $request The message request
     */
    public function sendEmail(Recipient $recipient, Request $request)
    {
        // send it
    }

    /**
     * Send an SMS reply to the Lead
     * 
     * @param Recipient $recipient That dude.
     * @param Request $request The message request
     */
    public function sendSms(Recipient $recipient, Request $request)
    {
        // shoop da whoop
    }

    /**
     * Complete the Call Back action
     * 
     * @param Recipient $recipient MY Man
     * @param Appointment $callback Callbacks are Appointments which is stupid.
     */
    public function markCalledBack(Recipient $recipient, Appointment $callback)
    {
        // make it so
    }

    /**
     * Send the Lead to the Service Department
     * 
     * @param Recipient $recipient That guy.
     */
    public function sendToServiceDepartment(Recipient $recipient)
    {
        // bye bye
    }

    /**
     * Send the Lead to the Crm System
     * 
     * @param Recipient $recipient Who to send.
     */
    public function sendToCrmSystem(Recipient $recipient)
    {
        // it's in the mail
    }

    /**
     * Update the notes for a Lead
     * 
     * @param Recipient $recipient The person.
     * @param Request $request The stuff
     */
    public function updateNotes(Recipient $recipient, Request $request)
    {
        // what's new
    }
}