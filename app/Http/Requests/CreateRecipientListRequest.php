<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRecipientListRequest extends FormRequest
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
            'uploaded_file_name' => 'required',
            'uploaded_file_headers' => 'required',
            'uploaded_file_fieldmap' => 'required',
            'pm_list_name' => 'required',
            'pm_list_type' => 'required|in:all_conquest,all_database,use_recipient_field',
        ];
    }
}
