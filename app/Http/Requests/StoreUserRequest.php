<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueEmailInCompany;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->input('role') == 'site_admin') {
            return auth()->user()->isAdmin();
        } else {
            return auth()->user()->isAdmin() || (get_active_company() && auth()->user()->isCompanyAdmin(get_active_company()));
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (auth()->user()->isAdmin()) {
            $possibleRoles = ['site_admin', 'admin', 'user'];
        } else {
            $possibleRoles = ['admin', 'user'];
        }
        return [
            'role' => [
                'required',
                Rule::in($possibleRoles)
            ],
            'email' => [
                'required',
                'email',
                new UniqueEmailInCompany(request()->route('company')->id ?? get_active_company())
            ]
        ];
    }
}
