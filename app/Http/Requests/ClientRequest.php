<?php

namespace App\Http\Requests;

use App\Http\Requests\UserRequest;

class ClientRequest extends UserRequest
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
}
