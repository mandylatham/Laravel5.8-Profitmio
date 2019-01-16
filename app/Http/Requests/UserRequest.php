<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'access' => 'required|in:admin,company_user',
            'role' => 'required_if:access,company_user|in:admin,user',
            'company' => 'required_if:access,company_user|exists:companies,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'nullable',
            'timezone' => 'required',
            'verify_password' => 'nullable|same:password'
        ];
    }
}
