<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTag extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    const CHECKED_IN_FROM_TEXT_TO_VALUE_TAG = 'checked-in-from-text-to-value';
    const VEHICLE_VALUE_REQUESTED_TAG = 'vehicle-value-requested-from-text-to-value';

    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = ['campaign_id', 'name'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Related campaign
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
