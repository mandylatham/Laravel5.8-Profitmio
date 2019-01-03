<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Sofa\Eloquence\Eloquence;

class Company extends Model
{
    use LogsActivity, Eloquence;

    const TYPE_SUPPORT = 'support';
    const TYPE_AGENCY = 'agency';
    const TYPE_DEALERSHIP = 'dealership';

    protected $searchableColumns = ['name', 'type', 'phone', 'id', 'address'];

    protected $fillable = [
        'name',
        'type',
        'phone',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'url',
        'facebook',
        'twitter',
    ];

    protected static $logAttributes = ['id', 'name', 'type'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public static function getAgencies()
    {
        return self::where('type', self::TYPE_AGENCY)->whereNull('deleted_at')->get();
    }

    public static function getDealerships()
    {
        return self::where('type', self::TYPE_DEALERSHIP)->whereNull('deleted_at')->get();
    }

    public function isAgency()
    {
        return $this->type === self::TYPE_AGENCY;
    }

    public function isDealership()
    {
        return $this->type === self::TYPE_DEALERSHIP;
    }

    public function isSupport()
    {
        return $this->type === self::TYPE_SUPPORT;
    }

    public function isUserProfileReady($userId)
    {
        return $this->users()->where('user_id', $userId)->whereNotNull('company_user.completed_at')->count() > 0;
    }

    public function getCampaigns($q = null)
    {
        $campaigns = Campaign::with(['dealership', 'agency'])
            ->where(function ($query) {
                if ($this->isAgency()) {
                    $query->where('agency_id', $this->id);
                } else if ($this->isDealership()) {
                    $query->where('dealership_id', $this->id);
                }
            })
            ->withCount(['recipients', 'email_responses', 'phone_responses', 'text_responses'])
            ->with(['dealership', 'agency'])
            ->whereNull('deleted_at')
            ->whereIn('status', ['Active', 'Completed', 'Upcoming']);

        if ($q) {
            $likeQ = '%' . $q . '%';
            $campaigns->where('name', 'like', $likeQ)
                ->orWhere('id', 'like', $likeQ)
                ->orWhere('starts_at', 'like', $likeQ)
                ->orWhere('ends_at', 'like', $likeQ)
                ->orWhere('order_id', 'like', $likeQ);
        }

        $campaigns->orderBy('campaigns.id', 'desc');

        return $campaigns;
    }
}
