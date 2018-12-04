<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isAdmin() || auth()->user()->isAgencyUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'order' => 'required|numeric',
            'start' => 'required|date|nullable',
            'end' => 'required|date|nullable',
            'expires' => 'nullable',
            'status' => 'alpha|required',
            'agency' => 'required',
            'client' => 'required',
            'enable_adf_crm_export' => 'alpha|size:2,3',
            'adf_crm_export' => 'required_with:enable_adf_crm_export',
            'enable_lead_alerts' => 'alpha|size:2,3',
            'lead_alert_emails' => 'required_with:enable_lead_alerts',
            'enable_client_passthrough' => 'alpha|size:2,3',
            'passthrough_email' => 'required_with:enable_client_passthrough',
            'enable_service_dept' => 'alpha|size:2,3',
            'service_dept_email' => 'required_with:enable_service_dept',
            'enable_sms_on_callback' => 'alpha|size:2,3',
            'sms_on_callback_number' => 'required_with:enable_service_dept',
            'phone_number_id' => 'nullable',
        ];
    }
}
