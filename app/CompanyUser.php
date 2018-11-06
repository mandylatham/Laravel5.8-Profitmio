<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    protected $casts = [
        'config' => 'json',
    ];

    protected $fillable = [
        'user_id',
        'company_id',
        'role',
        'config',
        'completed_at',
    ];

    protected $attributes = [
        'user_id',
        'company_id',
        'role',
        'config',
        'completed_at',
    ];
}
