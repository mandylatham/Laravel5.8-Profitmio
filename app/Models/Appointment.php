<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    const TYPE_CALLBACK = 'callback';
    const TYPE_APPOINTMENT = 'appointment';
}
