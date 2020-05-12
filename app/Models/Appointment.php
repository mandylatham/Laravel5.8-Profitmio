<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Appointment extends Model
{
    use SoftDeletes;

    protected $table = 'appointments';

    const TYPE_DISCUSSION = 'discussion';
    const TYPE_CALLBACK = 'callback';
    const TYPE_APPOINTMENT = 'appointment';

    /**
     * Fields to convert to Carbon instances
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'appointment_at'];

    protected $appends = ['appointment_at_formatted', 'name'];

    /**
     * Fields which can be filled
     * @var array
     */
    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'appointment_at',
        'first_name',
        'last_name',
        'phone_number',
        'alt_phone_number',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'auto_year',
        'auto_make',
        'auto_model',
        'auto_trim',
        'auto_mileage',
        'type',
        'called_back',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'called_back' => 'boolean',
    ];

    /**
     * Setting a complex attribute
     * @param $value
     */
    public function setAppointmentAtAttribute($value)
    {
        $this->attributes['appointment_at'] = null;

        if ($value) {
            $this->attributes['appointment_at'] = new Carbon($value);
        }
    }

    /**
     * Accessors
     */

    /**
     * Provide name rollup
     *
     * @return mixed
     */
    public function getNameAttribute()
    {
        $name = ucwords(strtolower($this->first_name . ' ' . $this->last_name));

        if (trim($name) == '') {
            $name = "No Name";
        }

        return $name;
    }

    /**
     * Provide vehicle rollup
     *
     * @return mixed
     */
    public function getVehicleAttribute()
    {
        $vehicle = ucwords(strtolower($this->auto_year . ' ' . $this->auto_make . ' ' . $this->auto_model));

        if (trim($vehicle) == '') {
            $vehicle = "No Vehicle";
        }

        return $vehicle;
    }

    public function getAppointmentAtFormattedAttribute()
    {
        return isset($this->appointment_at) ? $this->appointment_at->timezone(Auth::user()->timezone)->format("m/d/Y @ g:i A") : '';
    }

    /**
     * The Recipient which has this appointment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'recipient_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
