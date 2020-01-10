<?php

namespace App\Http\Controllers;

use App\Events\CampaignCountsUpdated;
use App\Events\AppointmentCreated;
use App\Classes\MailgunService;
use App\Factories\ActivityLogFactory;
use App\Mail\CrmAppointmentNotification;
use App\Mail\LeadNotification;
use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Recipient;
use App\Services\PusherBroadcastingService;
use App\Services\CampaignUserScoreService;
use App\Facades\TwilioClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Appointment Controller
 */
class AppointmentController extends Controller
{
    private const CALLBACK_MESSAGE = 'Profit Miner callback requested for %s at %s';

    private $activityFactory;

    private $appointment;

    private $carbon;

    private $campaign;

    private $company;

    private $log;

    private $recipient;

    private $mail;

    private $scoring;

    /**
     * AppointmentController constructor.
     * @param Appointment $appointment
     * @param Carbon      $carbon
     * @param Campaign    $campaign
     * @param Recipient   $recipient
     * @param Logger      $log
     * @param Mailer      $mail
     */
    public function __construct(
        ActivityLogFactory $activityFactory,
        Appointment $appointment,
        Carbon $carbon,
        Campaign $campaign,
        Company $company,
        Recipient $recipient,
        Logger $log,
        Mailer $mail,
        CampaignUserScoreService $scoring
    ) {
        $this->activityFactory = $activityFactory;
        $this->appointment = $appointment;
        $this->carbon = $carbon;
        $this->campaign = $campaign;
        $this->company = $company;
        $this->log = $log;
        $this->recipient = $recipient;
        $this->mail = $mail;
        $this->scoring = $scoring;
    }

    public function getCampaignIds()
    {
        $ids = $this->campaign->select('campaigns.id');
        $company = $this->company->findOrFail(get_active_company());
        $loggedUser = auth()->user();

        if ($company->isDealership()) {
            $ids->where('dealership_id', $company->id);
        } else if ($company->isAgency()) {
            $ids->where('agency_id', $company->id);
        }

        if (!$loggedUser->isCompanyAdmin($company->id)) {
            $ids->join('campaign_user', 'campaign_user.campaign_id', '=', 'campaigns.id')
                ->where('campaign_user.user_id', $loggedUser->id);
        }

        return $ids->get()->pluck('id')->toArray();
    }

    public function getForCalendarDisplay(Request $request)
    {
        $ids = $this->getCampaignIds();

        $appointments = Appointment::whereIn('campaign_id', $ids)
            ->whereNotNull('appointment_at')
            ->select('appointments.*');

        if ($request->has('start_date')) {
            $appointments->whereDate('appointment_at', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $appointments->whereDate('appointment_at', '<=', $request->input('end_date'));
        }
        return $appointments->paginate($request->input('per_page', 50));
    }

    /**
     * Unauthenticated API call to insert Appointments
     *
     * @param MailgunService $mailgun
     * @param Request        $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(MailgunService $mailgun, Request $request)
    {
        if (!$request->json()->get('campaign_id')) {
            $this->log->error("appointment failed to save, no campaign_id present: " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The requested communication failed to save.']);
        }

        $campaign = $this->campaign->find($request->json()->get('campaign_id'));

        if (!$campaign) {
            $this->log->error("appointment failed to save, no campaign_id present: " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The requested communication failed to save.']);
        }

        $phone_number = TwilioClient::getFormattedPhoneNumber($request->json()->get('phone_number'));
        if ($phone_number) {
            $phone_number = str_replace('+1', '', $phone_number->phoneNumber);
        }
        $alt_phone_number = TwilioClient::getFormattedPhoneNumber($request->json()->get('alt_phone_number'));
        if ($alt_phone_number) {
            $alt_phone_number = str_replace('+1', '', $alt_phone_number->phoneNumber);
        }
        $request_phone_number = str_replace('+1', '', $request->json()->get('phone_number'));

        $recipient = $this->recipient->where('campaign_id', $campaign->id)
            ->whereRaw("replace(phone, '+1', '') = ?", [$phone_number ?: $request_phone_number])
            ->first();

        if (!$recipient) {
            $recipient = new $this->recipient([
                'first_name'  => $request->json()->get('first_name'),
                'last_name'   => $request->json()->get('last_name'),
                'phone'       => $phone_number ?: $request->json()->get('phone_number'),
                'email'       => $request->json()->get('email'),
                'campaign_id' => $campaign->id,
            ]);

            $recipient->save();
        }

        $recipient->last_responded_at = $this->carbon->now('UTC');
        if ($request->json()->get('type') == 'appointment') {
            $recipient->appointment = 1;
        }

        $appointment_at = null;
        if (strlen($request->json()->get('appointment_at'))) {
            $appointment_at = $this->carbon->createFromFormat('Y-m-d G:i:s', $request->json()->get('appointment_at'), 'UTC');
        }

        $appointment = new $this->appointment([
            'recipient_id'     => $recipient->id,
            'campaign_id'      => $campaign->id,
            'appointment_at'   => $appointment_at,
            'auto_year'        => intval($recipient->year),
            'auto_make'        => $recipient->make,
            'auto_model'       => $recipient->model,
            'phone_number'     => $phone_number ?: $request->json()->get('phone_number'),
            'alt_phone_number' => $alt_phone_number ?: $request->json()->get('alt_phone_number'),
            'email'            => $request->json()->get('email'),
        ]);

        $allowed = ['first_name', 'last_name', 'address', 'city', 'state', 'zip', 'auto_trim', 'auto_mileage', 'type'];
        foreach ($allowed as $key) {
            $appointment->$key = $request->json()->get($key);
        }

        if (!$appointment->save()) {
            $this->log->error("AppointmentController@insert: unable to save ' . $appointment->type . ' for new request, " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The ' . $appointment->type . ' failed to save.']);
        }

        if ($appointment->type == 'appointment') {
            $recipient->appointment = true;
        }
        if ($appointment->type == 'callback') {
            $recipient->callback = true;
        }
        $recipient->save();

        event(new AppointmentCreated($appointment));
        event(new CampaignCountsUpdated($campaign));

        return response()->json(['error' => 0, 'message' => 'The ' . $appointment->type . ' has been saved.']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Pusher\PusherException
     */
    public function save(Request $request)
    {
        $data = $request->all();
        unset($data['created_at']);
        unset($data['deleted_at']);
        unset($data['updated_at']);

        if (isset($data['appointment_id']) && !empty($data['appointment_id'])) {
            $appt = $this->appointment->find($data['appointment_id']);
        } else {
            $appt = new $this->appointment();
        }

        foreach ($data as $field => $val) {
            if (empty($val)) {
                $appt->{$field} = null;
            } else {
                $appt->{$field} = $val;
            }
        }

        $appt->save();

        event(new CampaignCountsUpdated($campaign));

        return response()->json($appt->toJson());
    }

    /**
     * Add Call Data
     *
     * @param Appointment $appointment
     * @param Request     $request
     *
     * @return int|mixed [type]
     *
     * @throws \Pusher\PusherException
     */
    public function updateCalledStatus(Appointment $appointment, Request $request)
    {
        $appointment->called_back = (int)$request->called_back;

        $appointment->save();

        event(new CampaignCountsUpdated($appointment->campaign));

        return response()->json([
            'called_back' => $appointment->called_back,
        ]);
    }

    public function addAppointmentFromConsole(Campaign $campaign, Lead $lead, Request $request)
    {
        if ($request->has('appointment_date_time')) {
            $dateTime = explode(' ', $request->input('appointment_date_time'));
            $appointment_at = new Carbon($dateTime[0] . ' ' . $dateTime[1], 'UTC');
        } else {
            $appointment_at = new Carbon($request->input('appointment_date') . ' ' . $request->input('appointment_time'),
                Auth::user()->timezone);
        }

        $appointment = Appointment::create([
            'recipient_id'   => $lead->id,
            'campaign_id'    => $campaign->id,
            'first_name'     => $lead->first_name,
            'last_name'      => $lead->last_name,
            'appointment_at' => $appointment_at->timezone('UTC'),
            'auto_year'      => intval($lead->year),
            'auto_make'      => $lead->make,
            'auto_model'     => $lead->model,
            'phone_number'   => $lead->phone,
            'email'          => $lead->email,
            'type'           => 'appointment',
        ]);

        $lead->update(['appointment' => true, 'last_responded_at' => \Carbon\Carbon::now('UTC')]);

        event(new AppointmentCreated($appointment));
        event(new CampaignCountsUpdated($campaign));

        // Add User Score
        $activity = $this->activityFactory->forUserAddedLeadAppointment($lead, $appointment);
        $this->scoring->forActivity($activity);

        return $appointment;
    }
}
