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
            'start' => 'required_with:end|date|nullable',
            'end' => 'required_with:start|date|nullable',
            'status' => 'alpha|required',
            'agency' => 'required',
            'client' => 'required',
            'enable_adf_crm_export' => 'alpha|size:2,3',
            'adf_crm_export' => 'required_with:enable_adf_crm_export',
            'enable_lead_alerts' => 'alpha|size:2,3',
            'lead_alert_emails' => 'required_with:enable_lead_alerts',
            'enable_client_passthrough' => 'alpha|size:2,3',
            'passthrough_email' => 'required_with:enable_client_passthrough',
            'phone_number_id' => 'nullable',
        ];
    }
}
