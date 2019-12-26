<?php

namespace App\Models;

use Illuminate\Http\Request;
use Spatie\Activitylog\Traits\LogsActivity;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Company extends \ProfitMiner\Base\Models\Company implements HasMedia
{
    use LogsActivity, Eloquence, SoftDeletes, HasMediaTrait;

    protected $searchableColumns = ['name', 'type', 'phone', 'id', 'address'];

    protected $appends = ['image'];

    protected static $logAttributes = ['id', 'name', 'type'];

    public function activeCampaigns()
    {
        return $this->campaigns()->where('status', 'Active');
    }

    public function getImageAttribute()
    {
        $image = $this->getMedia('company-photo')->last();
        if ($image) {
            return $image->getFullUrl();
        }
        return '';
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function templates()
    {
        return $this->hasMany(CampaignScheduleTemplate::class);
    }

    public function scopeCampaigns()
    {
        return Campaign::where(function ($query) {
                if ($this->isAgency()) {
                    $query->where('agency_id', $this->id);
                } else if ($this->isDealership()) {
                    $query->where('dealership_id', $this->id);
                }
            });
    }

    public static function getAgencies()
    {
        return self::where('type', self::TYPE_AGENCY)
            ->whereNull('deleted_at')
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDealerships()
    {
        return self::where('type', self::TYPE_DEALERSHIP)
            ->whereNull('deleted_at')
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
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
            ->withCount(['recipients', 'emailResponses', 'phoneResponses', 'textResponses'])
            ->with(['dealership', 'agency'])
            ->whereNull('deleted_at')
            ->whereIn('status', ['Active', 'Completed', 'Upcoming', 'Expired']);

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

    public static function searchByRequest(Request $request)
    {
        $loggedUser = auth()->user();
        $query = self::query();
        if ($loggedUser->isAdmin() && $request->has('user')) {
            $companiesId = User::findOrFail($request->input('user'))
                ->companies()
                ->select('companies.id')
                ->get()
                ->pluck('id');
            $query->whereIn('id', $companiesId);
        } else if (!$loggedUser->isAdmin() && $request->has('user')) {
            $query->where('id', get_active_company());
        } else if (!$loggedUser->isAdmin()) {
            $campaignsCompanyIds = Campaign::select('dealership_id', 'agency_id')
                ->where(function ($query) {
                    return $query->where('agency_id', get_active_company())
                        ->orWhere('dealership_id', get_active_company());
                })
                ->get()
                ->toArray();
                if ($request->has('user') && $loggedUser->id !== $request->input('user')->id) {
                    $campaignsCompanyIds = array_where(array_unique(array_flatten($campaignsCompanyIds)), function ($id) {
                        return $id !== get_active_company();
                    });
                }
            $query->whereIn('id', $campaignsCompanyIds);
        }

        if ($request->has('q')) {
            $query->filterByQuery($request->input('q'));
        }
        return $query;
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    public function registerMediaCollections()
    {
        $disk = env('APP_ENV') == 'local' ? 'public' : 'media_public';

        $this
            ->addMediaCollection('company-image')
            ->useDisk($disk);
    }
}

