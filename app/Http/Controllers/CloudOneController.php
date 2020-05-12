<?php

namespace App\Http\Controllers;

use App\Events\CampaignCountsUpdated;
use App\Events\AppointmentCreated;
use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;

/**
 * Appointment Controller
 */
class CloudOneController extends Controller
{

    public function __construct() {}

    public function handleWebhook(Request $request)
    {
        if (!$this->supportedWebhook($request)) {
            return;
        }

        $campaign = Campaign::where('cloud_one_campaign_id', $request->input('campaign_id'))
            ->where('enable_call_center', true)
            ->first();
        if (!$campaign) {
            Log::error("appointment failed to save, no campaign found for cloud_one_campaign_id: " . $request->input('campaign_id'));
            return response()->json(['error' => 1, 'message' => 'The requested communication failed to save.']);
        }

        $requestPhone = $request->json()->get('phone_home') ?? $request->json()->get('phone_work') ?? $request->json()->get('phone_cell');
        $data = [
            'first_name'  => $request->input('name_first', '') ?? '',
            'last_name'   => $request->input('name_last', '') ?? '',
            'phone'       => $requestPhone,
            'email'       => $request->input('email_address', '') ?? '',
            'campaign_id' => $campaign->id,
        ];
        $recipient = $campaign->findOrCreateRecipientByPhone($requestPhone, $data);

        if ($request->input('lead_status')['status'] === 'Appointment Canceled') {
            return $this->cancelAppointment($campaign, $recipient);
        }

        return $this->createAppointmentOrCallback($request, $campaign, $recipient);
    }

    private function cancelAppointment(Campaign $campaign, Recipient $recipient)
    {
        Appointment::where('recipient_id', $recipient->id)
            ->where('campaign_id', $campaign->id)
            ->where('type', 'appointment')
            ->delete();

        return response()->json(['error' => 0, 'message' => 'The appointment for ' . $recipient->name . ' has been cancelled.']);

    }

    private function createAppointmentOrCallback(Request $request, Campaign $campaign, Recipient $recipient)
    {
        $type = $request->input('lead_status')['status'] === 'Dealer Needs To Call Back' ? 'callback' : 'appointment';

        $phoneNumber = $recipient->phone ?? $request->input('phone_home') ?? $request->input('phone_work') ?? $request->input('phone_cell');

        $recipient->last_responded_at = Carbon::now('UTC');
        $recipient->appointment = 1;

        $appointmentAt = null;
        if ($type === 'appointment' && $request->filled('appointment_date')) {
            $appointmentAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('appointment_date'), 'UTC');
        }
        if ($type === 'appointment' && !$request->filled('appointment_date')) {
            return response()->json([
                'error' => 1,
                'message' => 'appointment_date should not be empty.'
            ], 422);
        }

        $appointment = $recipient->appointments()->where('type', $type)->first();

        if (!$appointment) {
            $appointment = new Appointment();
            $appointment->recipient_id = $recipient->id;
            $appointment->campaign_id = $campaign->id;
            $appointment->appointment_at = $appointmentAt;
            $appointment->auto_year = intval($recipient->year);
            $appointment->auto_make = $recipient->make;
            $appointment->auto_model = $recipient->model;
            $appointment->phone_number = $phoneNumber;
            $appointment->alt_phone_number = null;
            $appointment->type = $type;
            $appointment->email = $request->json()->get('email_address');
        }

        $allowed = ['first_name', 'last_name', 'address', 'city', 'state', 'zip'];
        $allowedMap = ['name_first', 'name_last', 'street_address', 'city', 'state', 'zip_address'];
        foreach ($allowed as $idx => $key) {
            $appointment->$key = $request->input($allowedMap[$idx]);
        }

        if (!$appointment->save()) {
            Log::error("AppointmentController@insert: unable to save ' . $appointment->type . ' for new request, " . json_encode($request->all(),
                    JSON_UNESCAPED_SLASHES));
            return response()->json(['error' => 1, 'message' => 'The ' . $appointment->type . ' failed to save.'], 500);
        }

        if (is_null($recipient->status) || $recipient->status === Recipient::NOT_MARKETED_STATUS) {
            $recipient->status = Recipient::NEW_STATUS;
            $recipient->last_status_changed_at = Carbon::now()->toDateTimeString();
        }

        $recipient->save();

        event(new AppointmentCreated($appointment));
        event(new CampaignCountsUpdated($campaign));

        return response()->json(['error' => 0, 'message' => 'The ' . $appointment->type . ' has been saved.']);
    }

    private function getRecipient(Campaign $campaign, Request $request)
    {
        $recipient = $campaign->recipients()
            ->where(function ($query) use ($request) {
                $query->whereRaw("replace(phone, '+1', '') = ?", [$request->json()->get('phone_home')])
                    ->orWhereRaw("replace(phone, '+1', '') = ?", [$request->json()->get('phone_work')])
                    ->orWhereRaw("replace(phone, '+1', '') = ?", [$request->json()->get('phone_cell')]);
            })
            ->first();
        if (!$recipient) {
            $recipient = new Recipient([
                'first_name'  => $request->json()->get('name_first'),
                'last_name'   => $request->json()->get('name_last'),
                'phone'       => $request->json()->get('phone_home') ?? $request->json()->get('phone_work') ?? $request->json()->get('phone_cell'),
                'email'       => $request->json()->get('email_address'),
                'campaign_id' => $campaign->id,
            ]);
            $recipient->save();
        }
        return $recipient;
    }

    private function supportedWebhook(Request $request) {
        return $request->filled('lead_status') &&
            (
                $request->input('lead_status')['status'] === 'Appointment' ||
                $request->input('lead_status')['status'] === 'Appointment Canceled' ||
                $request->input('lead_status')['status'] === 'Dealer Needs To Call Back'
            );
    }
}
