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

        return !! auth()->user()->getCampaigns()->whereCampaignId($lead->campaign_id)->count() || auth()->user()->isAdmin();
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
