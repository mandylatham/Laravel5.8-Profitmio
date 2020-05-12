<?php

namespace App\Models;

use Illuminate\Http\Request;
use Sofa\Eloquence\Eloquence;
use Illuminate\Support\Facades\DB;
use App\Events\RecipientLabelAdded;
use App\Events\CampaignCountsUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends \ProfitMiner\Base\Models\Recipient
{
    use SoftDeletes, Eloquence;

    const NOT_MARKETED_STATUS = 'not-marketed';
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

    public $dates = ['last_responded_at', 'last_status_change_at'];

    public $casts = [
        'tags' => 'array',
    ];

    /** BEGIN ATTRIBUTES BLOCK **/
    public function getStatusForHumansAttribute()
    {
        if ($this->status === self::UNMARKETED_STATUS) return 'Uploaded';

        if ($this->status === self::MARKETED_STATUS) return 'Contacted';

        if ($this->status === self::NEW_STATUS) return 'New';

        if ($this->status === self::OPEN_STATUS) return 'Open';

        if ($this->status === self::CLOSED_STATUS) return 'Closed';

        return 'ERR';
    }
    /** END ATTRIBUTES BLOCK **/

    /** BEGIN RELATIONSHIPS BLOCK **/

    public function activities()
    {
        return $this->hasMany(RecipientActivity::class, 'recipient_id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'recipient_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function drops()
    {
        return $this->belongsToMany(Drop::class, 'deployment_recipients', 'recipient_id', 'deployment_id');
    }

    public function list()
    {
        return $this->belongsTo(RecipientList::class, 'recipient_list_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'recipient_id', 'id');
    }

    public function suppressions()
    {
        return $this->hasMany(SmsSuppression::class, 'phone', 'phone');
    }

    public function qrCode()
    {
        return $this->hasOne(RecipientQrCode::class, 'recipient_id', 'id');
    }

    public function textToValue()
    {
        return $this->hasOne(RecipientTextToValue::class, 'recipient_id', 'id');
    }

    public function textToValueOptIns()
    {
        return $this->hasMany(TextToValueOptIn::class, 'recipient_id', 'id');
    }

    /** END RELATIONSHIPS BLOCK **/

    /** BEGIN SCOPES BLOCK **/

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

    public function scopeLabelled($query, $labels)
    {
        return $query->whereJsonContains('tags', $labels);
    }

    /** END SCOPES BLOCK **/

    /** BEGIN ACTIONS BLOCK **/

    public function getCheckInUrl()
    {
        return route('lead.check-in', ['lead' => $this->id]);
    }

    public static function searchByRequest(Request $request, RecipientList $recipientList)
    {
        $query = $recipientList->recipients();

        if ($request->has('q')) {
            $query->filterByQuery($request->get('q'));
        }

        return $query;
    }

    public function markInvalidEmail()
    {
        $this->email = '';

        $this->save();
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
     * Get datetime of last lead reply dialog
     *
     * @param string $type "text", "email", or null (any)
     *
     * @return \App\Models\Response|null
     */
    public function getLastInboundDialogStart($type = null)
    {
        $response = $this->getLastDialogStartResponse(true, $type);

        return $response ? $response->created_at : null;
    }

    /**
     * Get datetime of last company response dialog
     *
     * @param string $type "text", "email", or null (any)
     *
     * @return \App\Models\Response|null
     */
    public function getLastOutboundDialogStart($type = null)
    {
        $response = $this->getLastDialogStartResponse(false, $type);

        return $response ? $response->created_at : null;
    }

    /**
     * Get the last Response beginning a dialog
     *
     * @param boolean $inbound Get last lead dialog start (true) or company response dialog start (false)
     * @param string  $type    "text", "email", or null (any)
     *
     * @return \App\Models\Response|null
     */
    private function getLastDialogStartResponse($inbound = true, $type = null)
    {
        $responseQuery = $this->responses();
        if ($type) $responseQuery->whereType($type);
        $responses = $responseQuery->select(['incoming', 'created_at'])
                                   ->orderBy('id', 'asc')
                                   ->get();

        $leadDialogStarts = collect([$responses->first()]);
        $companyDialogStarts = collect([]);

        for ($i=0; $i < $responses->count(); $i++) {
            if ($i < 1) continue;
            if ($responses[$i]->incoming == 1 && $responses[$i-1]->incoming == 0) {
                $leadDialogStarts->add($responses[$i]);
            }
            if ($responses[$i]->incoming == 0 && $responses[$i-1]->incoming == 1) {
                $companyDialogStarts->add($responses[$i]);
            }
        }

        return $inbound ? $leadDialogStarts->last() : $companyDialogStarts->last();
    }


    /** END ACTIONS BLOCK **/
}
