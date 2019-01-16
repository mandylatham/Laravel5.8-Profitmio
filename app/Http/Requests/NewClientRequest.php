<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->isAdmin or \Auth::user()->isAgency;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organization' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
            'timezone' => 'required',
            'verify_password' => 'required|same:password'
        ];
    }
}
