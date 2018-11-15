<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddRecipientRequest extends FormRequest
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
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required_without:phone|email|nullable',
            'phone' => 'required_without:email|between:10,12|nullable',
            'address1' => 'nullable',
            'city' => 'nullable',
            'state' => 'alpha|size:2|nullable',
            'zip' => 'nullable',
            'year' => 'digits:4|nullable',
            'make' => 'alpha_dash|nullable',
            'model' => 'alpha_dash|nullable',
			'vin' => 'nullable',
        ];
    }
}
