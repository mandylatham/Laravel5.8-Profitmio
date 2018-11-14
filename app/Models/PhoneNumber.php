<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PhoneNumber extends Model
{
    protected $table = 'phone_numbers';
}
