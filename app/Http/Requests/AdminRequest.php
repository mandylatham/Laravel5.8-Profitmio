<?php

namespace App\Http\Requests;

use App\Http\Requests\UserRequest;

class AdminRequest extends UserRequest
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
}
