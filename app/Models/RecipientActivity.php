<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientActivity extends Model
{
    protected $casts = [
        'action' => 'array',
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
