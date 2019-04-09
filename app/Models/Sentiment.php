<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentiment extends Model
{
    protected $fillable = [
        'positive', 'negative', 'neutral', 'mixed', 'sentiment',
    ];

    public function response()
    {
        return $this->belongsTo(Response::class);
    }
}
