<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueEmailInCompany;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isAdmin() || (get_active_company() && auth()->user()->isCompanyAdmin(get_active_company()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required|in:admin,user',
            'timezone' => [
                'required',
                Rule::in(User::getPossibleTimezonesForUser())
            ],
            'email' => [
                'required',
                'email',
                new UniqueEmailInCompany(request()->route('company')->id ?? get_active_company(), request()->route('user')->id)
            ]
        ];
    }
}
