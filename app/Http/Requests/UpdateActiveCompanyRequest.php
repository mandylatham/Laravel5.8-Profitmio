<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Company;

class UpdateActiveCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // If company is not passed, return true, so the validation is executed and force to select a company
        if (!$this->input('company')) {
            return true;
        }
        $company = Company::findOrFail($this->input('company'));
        return auth()->user()->belongsToCompany($company);
    }

    public function rules()
    {
        return [
            'company' => 'required'
        ];
    }
}
