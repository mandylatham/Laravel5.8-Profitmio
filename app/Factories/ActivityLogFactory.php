<?php

namespace App\Factories;

use App\Models\Lead;
use App\Models\Response;
use App\Models\User;
use Illuminate\Log\Logger;
use App\Models\Appointment;
use App\Models\LeadActivity;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Auth\Authenticatable;
use Lab404\Impersonate\Services\ImpersonateManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActivityLogFactory
{
    const LEAD_ACTIVITY_LOG = 'user-lead-management';

    /** @var Logger */
    private $log;

    /** @var User */
    private $user;

    /**
     * @todo fix imperation stripping
     * @param ImpersonateManager $manager
     * @param Logger $log
     */
    public function __construct(ImpersonateManager $manager, Logger $log)
    {
        $this->log = $log;
        $this->user = auth()->user();

        try {
            $this->user = $this->getImpersonationStrippedUser($manager);
        } catch (ModelNotFoundException $e) {
            $this->log->error('User impersonator not found');
            throw new \Exception('Unable to process action: improper user impersonation detected');
        }
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserOpenedLead(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->log(LeadActivity::OPENED);
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserClosedLead(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->log(LeadActivity::CLOSED);
    }

    /**
     * @param  Lead $lead
     * @return Activity|void
     */
    public function forUserReopenedLead(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->log(LeadActivity::REOPENED);
    }

    /**
     * @param Lead $lead
     * @param Response $response
     * @return Activity|void
     */
    public function forUserEmailedLead(Lead $lead, Response $response) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->withProperties([
                'response_id' => $response->id,
            ])
            ->log(LeadActivity::SENTEMAIL);
    }

    /**
     * @param Lead $lead
     * @param Response $response
     * @return Activity|void
     */
    public function forUserTextedLead(Lead $lead, Response $response) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->withProperties([
                'response_id' => $response->id,
            ])
            ->log(LeadActivity::SENTSMS);
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserSentLeadToCrm(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->log(LeadActivity::SENTTOCRM);
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserSentLeadToService(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->log(LeadActivity::SENTTOSERVICE);
    }

    /**
     * @param Lead $lead
     * @param Appointment $appointment
     * @return Activity|void
     */
    public function forUserAddedLeadAppointment(Lead $lead, Appointment $appointment) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->withProperties([
                'appointment_id' => $appointment->id,
            ])
            ->log(LeadActivity::ADDEDAPPOINTMENT);
    }

    /**
     * @param Lead $lead
     * @param Appointment $appointment
     * @return Activity|void
     */
    public function forUserCalledLeadBack(Lead $lead, Appointment $callback) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->user)
            ->withProperties([
                'appointment_id' => $callback->id,
            ])
            ->log(LeadActivity::CALLEDBACK);
    }

    /**
     * Get the user stripping impersonation
     *
     * @param  ImpersonateManager $manager
     * @return User
     * @throws ModelNotFoundException|
     */
    private function getImpersonationStrippedUser(ImpersonateManager $manager) : ?Authenticatable
    {
        if ($manager->isImpersonating()) {
            return User::firstOrFail($manager->getImpersonatorId());
        }

        return auth()->user();
    }
}
