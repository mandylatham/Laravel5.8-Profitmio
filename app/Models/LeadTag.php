<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTag extends Model
{
    public $timestamps = false;

    protected $guarded = [];

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
