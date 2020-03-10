<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;

class CloseLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $lead = $this->route('lead');

        $authUser = auth()->user();

        if ($authUser->isAdmin() || (!$authUser->isAdmin() && $authUser->isCompanyAdmin(get_active_company()))) {
            return true;
        } else if (!$authUser->isAdmin() && $authUser->isCompanyUser(get_active_company())) {
            return auth()->user()->getCampaigns()->whereCampaignId($lead->campaign_id)->count() > 0;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'outcome' => 'required|in:positive,negative',
            'tags' => 'array',
        ];
    }
}
