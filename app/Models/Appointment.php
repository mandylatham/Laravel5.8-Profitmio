<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $table = 'appointments';

    const TYPE_CALLBACK = 'callback';
    const TYPE_APPOINTMENT = 'appointment';

    /**
     * Fields to convert to Carbon instances
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'appointment_at'];

    /**
     * Fields which can be filled
     * @var array
     */
    protected $fillable = [
        'campaign_id', 'recipient_id', 'appointment_at', 'first_name', 'last_name', 'phone_number',
        'alt_phone_number', 'email', 'address', 'city', 'state', 'zip', 'auto_year', 'auto_make',
        'auto_model', 'auto_trim', 'auto_mileage', 'type', 'called_back'
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

    /**
     * The Recipient which has this appointment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'target_id', 'target_id');
    }
}
