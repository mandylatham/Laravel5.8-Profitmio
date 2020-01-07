<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientQrCode extends Model
{
    protected $table = 'recipient_qr_codes';

    protected $fillable = [
        'image_url'
    ];

    public function getCheckInUrl()
    {
        return env('APP_URL') . '/lead/' . $this->recipient_id . '/check-in';
    }
}
