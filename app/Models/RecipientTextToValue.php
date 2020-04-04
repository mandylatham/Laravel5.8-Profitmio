<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use QrCode;

class RecipientTextToValue extends Model
{
    protected $table = 'recipient_text_to_value';

    protected $fillable = [
        'text_to_value_code',
        'text_to_value_amount'
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'recipient_id', 'id');
    }
}
