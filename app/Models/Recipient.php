<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence;

class Recipient extends Model
{
    use SoftDeletes, Eloquence;

    protected $table = 'recipients';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'archived_at',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address1',
        'city',
        'state',
        'zip',
        'year',
        'make',
        'model',
        'campaign_id',
        'interested',
        'not_interested',
        'service',
        'wrong_number',
        'car_sold',
        'heat',
        'appointment',
        'notes',
        'last_responded_at',
        'carrier',
        'subgroup',
        'from_dealer_db',
        'callback',
    ];

    protected $appends = [
        'last_seen_ago',
        'name',
        'vehicle',
        'location',
        'labels_list',
        'labels_list_text'
    ];

    public static $mappable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address1',
        'city',
        'state',
        'zip',
        'make',
        'model',
        'vin',
    ];

    protected $searchableColumns = ['first_name', 'last_name', 'email', 'phone', 'address1', 'city', 'state', 'zip', 'year', 'make', 'model', 'vin'];

    public function list()
    {
        return $this->belongsTo(RecipientList::class, 'recipient_list_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'recipient_id', 'id');
    }

    public function drops()
    {
        return $this->belongsToMany(Drop::class, 'deployment_recipients', 'recipient_id', 'deployment_id');
    }

    public function responses()
    {
        return $this->hasOne(Response::class, 'recipient_id', 'recipient_id');
    }

    public function suppressions()
    {
        return $this->hasMany(SmsSuppression::class, 'phone', 'phone');
    }

    public function getDroppedTime()
    {
        $dropped = DeploymentRecipients::join('campaign_schedules', 'deployment_recipients.deployment_id', '=', 'campaign_schedules.id')
            ->where('campaign_schedules.campaign_id', $this->campaign_id)
            ->where('deployment_recipients.recipient_id', $this->id)
            ->whereNotNull('deployment_recipients.sent_at')
            ->first();
        return $dropped ? $dropped->sent_at : null;
    }

    /**
     * Accessors
     */
    public function getVehicleAttribute()
    {
        return $this->year . ' ' . $this->make . ' ' . $this->model;
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getLastSeenAgoAttribute()
    {
        $tz = isset(Auth::user()->timezone) ?: 'America/New_York';

        return $this->last_seen ? (new Carbon($this->last_seen))->timezone($tz)->diffForHumans(Carbon::now(),
                true) . ' ago' : '';
    }

    public function getLocationAttribute()
    {
        $location = [];
        if (!empty($this->city)) {
            $location[] = $this->city;
        }
        if (!empty($this->state)) {
            $location[] = $this->state;
        }

        return implode(', ', $location);
    }

    public function getLabelsListAttribute()
    {
        $labels = [];

        if ((bool)$this->interested) {
            $labels['interested'] = 'Interested';
        }

        if ((bool)$this->not_interested) {
            $labels['not_interested'] = 'Not Interested';
        }

        if ((bool)$this->service) {
            $labels['service'] = 'Service Dept';
        }

        if ((bool)$this->heat) {
            $labels['heat'] = 'Heat Case';
        }

        if ((bool)$this->car_sold) {
            $labels['car_sold'] = 'Car Sold';
        }

        if ((bool)$this->wrong_number) {
            $labels['wrong_number'] = 'Wrong Number';
        }

        return $labels;
    }

    public function getLabelsListTextAttribute()
    {
        $string = '';

        $labelsArray = $this->getLabelsListAttribute();

        if ($labelsArray) {
            $string = implode(', ', $labelsArray);
        }

        return $string;
    }

    public function getLabelsListHtmlAttribute()
    {
        $html = '';

        $labelsArray = $this->getLabelsListAttribute();

        if ($labelsArray) {
            $html .= '<ul class="labels">';
            foreach ($labelsArray as $item) {
                $html .= "<li>{$item}</li>";
            }
            $html .= '</ul>';
        }

        return $html;
    }

    public function getEmailAttribute()
    {
        return strtolower($this->attributes['email']);
    }

    public static function searchByRequest(Request $request, RecipientList $recipientList)
    {
        $query = $recipientList->recipients();

        if ($request->has('q')) {
            $query->filterByQuery($request->get('q'));
        }

        return $query;
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    public function scopeWithResponses($query, $campaignId)
    {
        return $query->whereIn('recipients.id',
            result_array_values(
                DB::select("
                    select distinct(recipient_id) from responses where campaign_id = {$campaignId}
                ")
            )
        );
    }

    public function scopeUnread($query, $campaignId)
    {
        return $query->whereIn('recipients.id',
            result_array_values(
                DB::select("
                    select recipient_id from responses where id in (
                    select max(id) from responses where campaign_id={$campaignId} and `read` = 0 and type <> 'phone' group by recipient_id
                    ) and incoming = 1 and `read` = 0
                ")
            )
        );
    }

    public function scopeIdle($query, $campaignId)
    {
        return $query->whereIn('recipients.id',
            result_array_values(
                DB::select("
                    select recipient_id from responses where id in (
                    select max(id) from responses where campaign_id={$campaignId} and `incoming` = 0 group by recipient_id
                    ) and incoming = 0
                ")
            )
        );
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeLabelled($query, $campaignId, $label)
    {
        if ($label == 'none') {
            return $query->where('recipients.campaign_id', $campaignId)->where([
                'interested'     => 0,
                'not_interested' => 0,
                'service'        => 0,
                'heat'           => 0,
                'appointment'    => 0,
                'car_sold'       => 0,
                'wrong_number'   => 0,
                'callback'       => 0,
            ]);
        }

        return $query->where('recipients.campaign_id', $campaignId)->where($label, 1);
    }

    public function scopeSearch($query, $searchString)
    {
    }

    public function markInvalidEmail()
    {
        $this->email = '';

        $this->save();
    }
}
