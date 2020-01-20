<?php

namespace App\Factories;

use App\Models\Impersonation\ImpersonatedUser;
use App\Models\Lead;
use App\Models\Response;
use App\Models\User;
use Illuminate\Log\Logger;
use App\Models\Appointment;
use App\Models\LeadActivity;
use Spatie\Activitylog\Models\Activity;
use Lab404\Impersonate\Services\ImpersonateManager;

class ActivityLogFactory
{
    const LEAD_ACTIVITY_LOG = 'user-lead-management';

    /** @var Logger */
    private $log;

    /** @var User */
    private $user;

    private $impersonationManager;

    /**
     * @todo fix imperation stripping
     * @param ImpersonateManager $manager
     * @param Logger $log
     */
    public function __construct(ImpersonateManager $manager, Logger $log)
    {
        $this->log = $log;
        $this->impersonationManager = $manager;
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserOpenedLead(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->getActivityUser())
            ->log(LeadActivity::OPENED);
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserCheckedLeadIn(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->getActivityUser())
            ->log(LeadActivity::CHECKED_IN);
    }

    /**
     * @param Lead $lead
     * @return Activity|void
     */
    public function forUserClosedLead(Lead $lead) : ?Activity
    {
        return activity(self::LEAD_ACTIVITY_LOG)
            ->performedOn($lead)
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
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
            ->causedBy($this->getActivityUser())
            ->withProperties([
                'appointment_id' => $callback->id,
            ])
            ->log(LeadActivity::CALLEDBACK);
    }

    public function getActivityUser()
    {
        if ($this->impersonationManager->isImpersonating()) {
            $impersonationRecord = ImpersonatedUser::find($this->impersonationManager->getImpersonatorId());
            if ($impersonationRecord) {
                return $impersonationRecord->impersonatorUser;
            }
        }
        return auth()->user();
    }
}
