<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneNumber extends Model
{
    use SoftDeletes;

    protected $table = 'phone_numbers';

    public $fillable = [
        'client_id', 'campaign_id', 'phone_number', 'forward', 'sid', 'region', 'state', 'zip', 'call_source_name'
    ];

    public function getIdAttribute()
    {
        return $this->phone_number_id;
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }
}
