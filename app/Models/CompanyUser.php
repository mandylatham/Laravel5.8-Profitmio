<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyUser extends Pivot
{
    use LogsActivity;
    protected static $logAttributes = ['id', 'user_id', 'company_id', 'role', 'config', 'completed_at'];

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
