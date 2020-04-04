<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CannedResponse extends Model
{
    protected $table = 'canned_response';

    protected $fillable = [
        'response',
        'sentiment'
    ];
}
