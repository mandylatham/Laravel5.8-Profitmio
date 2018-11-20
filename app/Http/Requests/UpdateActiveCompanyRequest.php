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
