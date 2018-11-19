<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'type' => 'required|in:support,agency,dealership',
            'url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'image' => 'nullable|image'
        ];
    }
}
