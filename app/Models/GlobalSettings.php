<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Model
{
    protected $table = 'global_settings';

    protected $fillable = ['name', 'value'];
}
