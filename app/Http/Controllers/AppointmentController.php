<?php

namespace App\Http\Controllers;

use App\Events\CampaignCountsUpdated;
use App\Classes\MailgunService;
use App\Mail\CrmNotification;
use App\Mail\LeadNotification;
use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Recipient;
use App\Services\PusherBroadcastingService;
use App\Services\TwilioClient;
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

    private $appointment;

    private $carbon;

    private $campaign;

    private $company;

    private $log;

    private $recipient;

    private $mail;

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
        Appointment $appointment,
        Carbon $carbon,
        Campaign $campaign,
        Company $company,
        Recipient $recipient,
        Logger $log,
        Mailer $mail
    ) {
        $this->appointment = $appointment;
        $this->carbon = $carbon;
        $this->campaign = $campaign;
        $this->company = $company;
        $this->log = $log;
        $this->recipient = $recipient;
        $this->mail = $mail;
    }

    public function getCampaignIds()
    {
        $ids = $this->campaign->select('id');
        $company = $this->company->findOrFail(get_active_company());

        if ($company->isDealership()) {
            $ids->where('dealership_id', $company->id);
        } else {
            if ($company->isAgency()) {
                $ids->where('agency_id', $company->id);
            }
        }

        return $ids->get()->toArray();
    }

    public function getForCalendarDisplay(Request $request)
    {
        $ids = $this->getCampaignIds();

        $appointments = $this->appointment
            ->where('called_back', 0)
            ->whereIn('campaign_id', $ids)
            ->where('type', 'appointment')
            ->whereNotNull('appointment_at')
            ->selectRaw("concat('Campaign ', campaign_id, ': ', first_name,' ',last_name,': ',phone_number) as title, appointment_at as start, DATE(appointment_at) as date")
            ->get();

        return $appointments;
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

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);
        }

        $campaign = $this->campaign->find($request->json()->get('campaign_id'));

        if (!$campaign) {
            $this->log->error("appointment failed to save, no campaign_id present: " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);
        }

        $phone_number = TwilioClient::getFormattedPhoneNumber($request->json()->get('phone_number'));
        if ($phone_number) {
            $phone_number = $phone_number->phoneNumber;
        }
        $alt_phone_number = TwilioClient::getFormattedPhoneNumber($request->json()->get('alt_phone_number'));
        if ($alt_phone_number) {
            $alt_phone_number = $alt_phone_number->phoneNumber;
        }

        $recipient = $this->recipient->where('campaign_id', $campaign->id)
            ->where('phone', $phone_number ?: $request->json()->get('phone_number'))
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
            $appointment_at = $this->carbon->createFromFormat('Y-m-d G:i:s', $request->json()->get('appointment_at'),
                $campaign->client->timezone);
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
            $this->log->error("AppointmentController@insert: unable to save appointment for new request, " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);
        }

        if ($appointment->type == 'appointment') {
            $recipient->appointment = true;
        }
        if ($appointment->type == 'callback') {
            $recipient->callback = true;
        }
        $recipient->save();

        event(new CampaignCountsUpdated($campaign));

        if (in_array($appointment->type, [Appointment::TYPE_APPOINTMENT])) {
            if ($campaign->adf_crm_export) {
                $alert_emails = explode(',', $campaign->adf_crm_export_email);
                foreach ($alert_emails as $email) {
                    $email = trim($email);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->log->error("AppointmentController@insert (line 82): Skipping crm notification to invalid email, $email");
                        continue;
                    }
                    try {
                        $this->mail->to($email)->send(new CrmNotification($campaign, $appointment));
                        $this->log->debug("AppointmentController@insert: Sent crm alerts for appointment #{$appointment->id}");
                    } catch (\Exception $e) {
                        $this->log->error("Unable to send crm notification: " . $e->getMessage());
                    }
                }
            }

        }

        if ($campaign->lead_alerts) {
            $alert_emails = explode(',', $campaign->lead_alert_email);

            foreach ($alert_emails as $email) {
                $email = trim($email);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->log->error("AppointmentController@insert (line 82): Skipping lead notification to invalid email, $email");

                    continue;
                }

                try {
                    $this->mail->to($email)->send(new LeadNotification($campaign, $appointment));
                    $this->log->debug("AppointmentController@insert: Sent lead alerts for appointment #{$appointment->id}");
                } catch (\Exception $e) {
                    $this->log->error("Unable to send lead notification: " . $e->getMessage());
                }
            }
        }

        if (($appointment->type == Appointment::TYPE_CALLBACK) && ($campaign->sms_on_callback == 1)) {
            $from = $campaign->phone->phone_number;
            $to = $campaign->sms_on_callback_number;
            try {
                $message = $this->getCallbackMessage($appointment);
                TwilioClient::sendSms($from, $to, $message);
            } catch (\Exception $exception) {
                Log::error("Unable to send callback SMS: " . $e->getMessage());
            }
        }

        return response()->json(['error' => 0, 'message' => 'The appointment has been saved.']);
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
     * @param Appointment $appointment
     * @param Request     $request
     * @return int|mixed [type]
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

    /**
     * @param Campaign  $campaign
     * @param Recipient $recipient
     * @param Request   $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Pusher\PusherException
     */
    public function addAppointmentFromConsole(Campaign $campaign, Recipient $recipient, Request $request)
    {
        if ($request->has('appointment_date_time')) {
            $dateTime = explode(' ', $request->input('appointment_date_time'));

            $appointment_at = new Carbon($dateTime[0] . ' ' . $dateTime[1], Auth::user()->timezone);
        } else {
            $appointment_at = new Carbon($request->input('appointment_date') . ' ' . $request->input('appointment_time'),
                Auth::user()->timezone);
        }

        $appointment = Appointment::create([
            'recipient_id'   => $recipient->id,
            'campaign_id'    => $campaign->id,
            'first_name'     => $recipient->first_name,
            'last_name'      => $recipient->last_name,
            'appointment_at' => $appointment_at->timezone('UTC'),
            'auto_year'      => intval($recipient->year),
            'auto_make'      => $recipient->make,
            'auto_model'     => $recipient->model,
            'phone_number'   => $recipient->phone,
            'email'          => $recipient->email,
        ]);

        $recipient->update(['appointment' => true, 'last_responded_at' => \Carbon\Carbon::now('UTC')]);

        if (in_array($appointment->type, [Appointment::TYPE_APPOINTMENT])) {
            if ($campaign->adf_crm_export) {
                $alert_emails = explode(',', $campaign->adf_crm_export_email);
                foreach ($alert_emails as $email) {
                    $email = trim($email);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->log->error("AppointmentController@insert (line 82): Skipping crm notification to invalid email, $email");
                        continue;
                    }
                    try {
                        $this->mail->to($email)->send(new CrmNotification($campaign, $appointment));
                        $this->log->debug("AppointmentController@insert: Sent crm alerts for appointment #{$appointment->id}");
                    } catch (\Exception $e) {
                        $this->log->error("Unable to send crm notification: " . $e->getMessage());
                    }
                }

            }
        }

        event(new CampaignCountsUpdated($campaign));

        return response()->json([
            'appointment_at' => $appointment_at->timezone(Auth::user()->timezone)->format("m/d/Y h:i A"),
        ]);
    }

    /**
     * @param Appointment $appointment
     * @return string
     */
    private function getCallbackMessage(Appointment $appointment): string
    {
        $name = $appointment->first_name . ' ' . $appointment->last_name;

        return sprintf(self::CALLBACK_MESSAGE, $name, $appointment->phone_number);
    }
}
