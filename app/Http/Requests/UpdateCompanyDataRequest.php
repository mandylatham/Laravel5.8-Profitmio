<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueEmailInCompany;
use Illuminate\Validation\Rule;

class UpdateCompanyDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $possibleRoles = ['admin', 'user'];
        return [
            'company' => 'required',
            'role' => [
                'required',
                Rule::in($possibleRoles)
            ],
            'timezone' => [
                'required',
                Rule::in(User::getPossibleTimezonesForUser())
            ]
        ];
    }
}
