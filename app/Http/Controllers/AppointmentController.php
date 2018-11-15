<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Campaign;
use App\Classes\MailgunService;
use App\Mail\CrmNotification;
use App\Mail\LeadNotification;
use App\Recipient;
use Illuminate\Http\Request;

/**
 * Appointment Controller
 */
class AppointmentController extends Controller
{
    /**
     * Unauthenticated API call to insert Appointments
     *
     * @param App\Classes\MailgunService
     * @param Illuminate\Http\Request
     *
     * @return JSON
     */
    public function insert(MailgunService $mailgun, Request $request)
    {
        if (!$request->json()->get('campaign_id')) {
            \Log::error("appointment failed to save, no campaign_id present: " . json_encode($request->all(), JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);
        }

        $campaign = Campaign::find($request->json()->get('campaign_id'));

        if (! $campaign) {
            \Log::error("appointment failed to save, no campaign_id present: " . json_encode($request->all(), JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);
        }

        $phone_number = \Twilio::getFormattedPhoneNumber($request->json()->get('phone_number'));
        if ($phone_number) {
            $phone_number = $phone_number->phoneNumber;
        }
        $alt_phone_number = \Twilio::getFormattedPhoneNumber($request->json()->get('alt_phone_number'));
        if ($alt_phone_number) {
            $alt_phone_number = $alt_phone_number->phoneNumber;
        }

        $recipient = Recipient::where('campaign_id', $campaign->id)
            ->where('phone', $phone_number ?: $request->json()->get('phone_number'))
            ->first();

        if (!$recipient) {
            $recipient = new Recipient([
                'first_name' => $request->json()->get('first_name'),
                'last_name' => $request->json()->get('last_name'),
                'phone' => $phone_number ?: $request->json()->get('phone_number'),
                'email' => $request->json()->get('email'),
                'campaign_id' => $campaign->id,
            ]);

            $recipient->save();
        }

        $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
        if ($request->json()->get('type') == 'appointment') {
            $recipient->appointment = 1;
        }

        $appointment_at = null;
        if ( strlen($request->json()->get('appointment_at'))) {
            $appointment_at = \Carbon\Carbon::createFromFormat('Y-m-d G:i:s', $request->json()->get('appointment_at'), $campaign->client->timezone);
        }

        $appointment = new Appointment([
            'recipient_id' => $recipient->id,
            'campaign_id' => $campaign->id,
            'appointment_at' => $appointment_at,
            'auto_year' => intval($recipient->year),
            'auto_make' => $recipient->make,
            'auto_model' => $recipient->model,
            'phone_number' => $phone_number ?: $request->json()->get('phone_number'),
            'alt_phone_number' => $alt_phone_number ?: $request->json()->get('alt_phone_number'),
            'email' => $request->json()->get('email'),
        ]);

        $allowed = ['first_name', 'last_name', 'address', 'city', 'state', 'zip', 'auto_trim', 'auto_mileage', 'type'];
        foreach ($allowed as $key) {
            $appointment->$key = $request->json()->get($key);
        }

        if (!$appointment->save()) {
            \Log::error("AppointmentController@insert: unable to save appointment for new request, " . json_encode($request->all(), JSON_UNESCAPED_SLASHES));

            return response()->json(['error' => 1, 'message' => 'The appointment failed to save.']);

            abort(406);
        }

        $recipient->save();

	if (in_array($appointment->type, ['appointment', 'callback'])) {

		if ($campaign->adf_crm_export) {
		    $alert_emails = explode(',', $campaign->lead_alert_email);
		    foreach ($alert_emails as $email) {
			$email = trim($email);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    \Log::error("AppointmentController@insert (line 82): Skipping crm notification to invalid email, $email");

			    continue;
			}

			try {
			    \Mail::to($email)->send(new CrmNotification($campaign, $appointment));
			    \Log::debug("AppointmentController@insert: Sent crm alerts for appointment #{$appointment->id}");
			} catch (\Exception $e) {
			    \Log::error("Unable to send crm notification: " . $e->getMessage());
			}
		    }

		}

		if ($campaign->lead_alerts) {
		    $alert_emails = explode(',', $campaign->lead_alert_email);

		    foreach ($alert_emails as $email) {
			$email = trim($email);

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    \Log::error("AppointmentController@insert (line 82): Skipping lead notification to invalid email, $email");

			    continue;
			}

			try {
			    \Mail::to($email)->send(new LeadNotification($campaign, $appointment));
			    \Log::debug("AppointmentController@insert: Sent lead alerts for appointment #{$appointment->id}");
			} catch (\Exception $e) {
			    \Log::error("Unable to send lead notification: " . $e->getMessage());
			}
		    }

		}

	}

        return response()->json(['error' => 0, 'message' => 'The appointment has been saved.']);
    }

    public function save(Request $request)
    {
        $data = $request->all();
        unset($data['created_at']);
        unset($data['deleted_at']);
        unset($data['updated_at']);

        if (isset($data['appointment_id']) && !empty($data['appointment_id'])) {
            $appt = Appointment::find($data['appointment_id']);
        } else {
            $appt = new Appointment;
        }

        foreach ($data as $field => $val) {
            if (empty($val)) {
                $appt->{$field} = null;
            } else {
                $appt->{$field} = $val;
            }
        }

        $appt->save();

        return response()->json($appt->toJson());
    }

    /**
     * Add Call Data
     * @param  Appointment
     * @param  Request
     * @return [type]
     */
    public function updateCalledStatus(Appointment $appointment, Request $request)
    {
        $appointment->called_back = $request->called_back == 'true' ? 1 : 0;

        $appointment->save();

        return $appointment->called_back;
    }
}
