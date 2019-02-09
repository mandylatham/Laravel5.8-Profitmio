<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'areaCode' => 'digits:3|required_without_all:inPostalCode,contains|nullable',
            'inPostalCode' => 'digits:5|required_without_all:areaCode,contains|nullable',
            'contains' => 'alpha|required_without_all:areaCode,inPostalCode|nullable',
            'country' => [
                'required',
                Rule::in(['US', 'CA']),
            ],
        ];
    }

    /**
     * Defined custom messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'areaCode.required_without_all' => 'This search requires at least an Area Code, Zip Code, or a "Contains" string'
        ];
    }
}
