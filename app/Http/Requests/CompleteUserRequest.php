<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class CompleteUserRequest extends FormRequest
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
        $user = User::find($this->input('user'));
        if ($user->isAdmin()) {
            return [];
        }
        if (!$user->isProfileCompleted()) {
            return [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'timezone' => 'required',
                'password' => 'required|string|min:6|confirmed',
            ];
        } else {
            return [
                'timezone' => 'required'
            ];
        }
    }
}
