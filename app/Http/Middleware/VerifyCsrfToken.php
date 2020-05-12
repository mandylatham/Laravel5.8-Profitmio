<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'text-responses/inbound', 'email-responses/inbound', 'phone-responses/inbound',
        'phone-responses/status', 'email-responses/log', 'appointments/insert', 'appointments/get',
        'appointments/save', 'text-in', 'cloudone'
    ];
}
