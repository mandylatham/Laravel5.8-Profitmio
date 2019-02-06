<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;


class Mailer extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
        'name', 'type', 'size', 'in_home_at',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'id', 'campaign_id');
    }
}
