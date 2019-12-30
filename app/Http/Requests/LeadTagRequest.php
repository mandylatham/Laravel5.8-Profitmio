<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadTagRequest extends FormRequest
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
            'name' => 'required',
            'text' => 'required',
            'indication' => 'required|in:positive,negative,neutral',
        ];
    }
}
