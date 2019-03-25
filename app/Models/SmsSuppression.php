<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSuppression extends Model
{
    protected $table = 'sms_suppressions';

	protected $fillable = ['phone'];
}
