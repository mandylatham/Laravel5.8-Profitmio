<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\CampaignCountsUpdated;
use App\Events\RecipientLabelAdded;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence;

class Recipient extends \ProfitMiner\Base\Models\Recipient
{
    use SoftDeletes, Eloquence;

    const UNMARKETED_STATUS = 'unmarketed';
    const MARKETED_STATUS = 'marketed';
    const NEW_STATUS = 'new-lead';
    const OPEN_STATUS = 'open-lead';
    const CLOSED_STATUS = 'closed-lead';

    protected $searchable = ['first_name', 'last_name'];

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

    protected $searchableColumns = ['first_name', 'last_name', 'email', 'phone', 'status', 'address1', 'city', 'state', 'zip', 'year', 'make', 'model', 'vin'];

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
        return $this->hasMany(Response::class, 'recipient_id', 'id');
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
        return $query->whereIn('recipients.id', result_array_values(
                \DB::select("
                    select distinct(recipient_id) from responses where responses.id in (
                    select max(responses.id) from responses where campaign_id={$campaignId} and `read` = 0 and type <> 'phone' group by recipient_id
                    ) and incoming = 1 and `read` = 0
                ")
            ));
    }

    public function scopeCalls($query)
    {
        return $query->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'phone');
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

    public function scopeEmail($query)
    {
        return $query->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'email');
    }

    public function scopeSms($query)
    {
        return $query->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'text');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeLabelled($query, $label, $campaignId)
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
