<?php

namespace App\Models;

use Storage;
use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Support\Str;
use Sofa\Eloquence\Eloquence;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class User extends \ProfitMiner\Base\Models\User implements HasMedia, CanResetPassword
{
    use Notifiable, Impersonate, LogsActivity, Eloquence, HasMediaTrait, CanResetPasswordTrait;

    protected $searchableColumns = ['id', 'first_name', 'last_name', 'email', 'phone_number'];

    protected static $logAttributes = ['id', 'name', 'is_admin', 'email', 'campaigns', 'companies'];

    protected $appends = ['image_url', 'name'];

    protected $attributes = [];

    public function activate($companyId)
    {
        $rel = $this->invitations()->where('company_id', $companyId)->firstOrFail();
        $rel->is_active = true;

        $rel->save();
    }

    public function deactivate($companyId)
    {
        $rel = $this->invitations()->where('company_id', $companyId)->firstOrFail();
        $rel->is_active = false;

        $rel->save();
    }

    public function getCampaigns()
    {
        return Campaign::join('campaign_user', 'campaign_user.campaign_id', '=', 'campaigns.id')
            ->where('campaign_user.user_id', $this->id);
    }

    public function getActiveCampaignsForCompany(Company $company)
    {
        $campaignsId = \DB::table('campaign_user')
            ->where('user_id', $this->id)
            ->select('campaign_user.campaign_id')
            ->get()
            ->pluck('campaign_id');
        return Campaign::whereIn('id', $campaignsId)
            ->where('status', 'Active')
            ->where(function ($query) use ($company) {
                $query->where('dealership_id', $company->id)
                    ->orWhere('agency_id', $company->id);
            })
            ->get();
    }

    /**
     * Return the company that is selected by the logged user
     *
     * This method verify that the user belongs to selected company (prevents data leak)
     *
     * @return mixed
     */
    public function getActiveCompany()
    {
        return $this->companies()->where('companies.id', get_active_company())->first();
    }

    public function getActiveCompanies()
    {
        return $this->companies()->where('company_user.is_active', true)->orderBy('companies.name', 'asc')->get();
    }

    public function getImageUrlAttribute()
    {
        $image = $this->getMedia('profile-photo')->last();
        if ($image) {
            return Storage::disk($image->disk)->url($image->id.'/'.$image->file_name);
        }
        return '';
    }

    public function agencyCampaigns()
    {
        return $this->hasMany(Campaign::class, 'agency_id', 'id');
    }

    /**
     * Check if user belongs to given company
     * @param Company $company
     * @return bool
     */
    public function belongsToCompany(Company $company)
    {
        return $this->companies()->where('companies.id', $company->id)->count() === 1;
    }

    /**
     * The roles that belong to the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class)->using(CompanyUser::class)->withPivot('role', 'config', 'is_active', 'completed_at');
    }

    /**
     * The roles that belong to the user.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    /**
     * Return the list of users that logged user can see
     * Site Admin can see all users, company user can see company's user
     *
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getListOfUsers($companyId = null)
    {
        if ($this->isAdmin()) {
            return $companyId ? Company::findOrFail($companyId)->users : self::all();
        } else if ($this->isCompanyAdmin(get_active_company())) {
            return Company::findOrFail(get_active_company())->users;
        }
        return [];
    }

    public function getRole(Company $company)
    {
        if ($this->isAdmin()) {
            return 'site_admin';
        } else {
            return $this->invitations()->where('company_id', $company->id)->firstOrFail()->role;
        }
    }

    public function getTimezone(Company $company)
    {
        return $this->invitations()->where('company_id', $company->id)->firstOrFail()->config['timezone'];
    }

    public function countActiveCompanies()
    {
        return $this->invitations()->where('is_active', 1)->count();
    }

    public function hasActiveCompanies()
    {
        return $this->invitations()->where('is_active', 1)->count() > 0;
    }

    public function hasPendingInvitations()
    {
        if ($this->isAdmin()) {
            return false;
        } else {
            return $this->invitations()->whereNull('completed_at')->count() > 0;
        }
    }

    public function replies()
    {
        return $this->hasMany(Response::class);
    }

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPassword($token, $this));
    }

    public function invitations()
    {
        return $this->hasMany(CompanyUser::class, 'user_id', 'id');
    }

    public function isActive($companyId)
    {
        return $this->invitations()->where('company_id', $companyId)->firstOrFail()->is_active;
    }

    public function isAdmin(): bool
    {
        return (bool)$this->is_admin;
    }

    public function isCompanyAdmin(int $companyId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        $company = $this->companies()->find($companyId);
        return $company && $company->pivot->role == self::ROLE_ADMIN;
    }

    public function isCompanyUser(int $companyId): bool
    {
        $company = $this->companies()->find($companyId);
        if (empty($company) || $company->pivot->role != self::ROLE_USER) {
            return false;
        }
        return true;
    }

    /**
     * Method that verify if user belongs to an agency company (user or admin)
     * @param int|null $companyId Id of company if we want to verify specific company
     * @return bool
     */
    public function isAgencyUser(int $companyId = null): bool
    {
        $userCompanies = $this->companies()
            ->where('companies.type', 'agency');
        if ($companyId) {
            $userCompanies->where('companies.id', $companyId);
        }
        return $userCompanies->count() > 0;
    }


    /**
     * Method that verify if user belongs to an dealership company (user or admin)
     * @param int|null $companyId Id of company if we want to verify specific company
     * @return bool
     */
    public function isDealershipUser(int $companyId = null): bool
    {
        $userCompanies = $this->companies()
            ->where('companies.type', 'dealership');
        if ($companyId) {
            $userCompanies->where('companies.id', $companyId);
        }
        return $userCompanies->count() > 0;
    }

    public function isProfileCompleted()
    {
        return $this->password !== '';
    }

    public function isCompanyProfileReady(Company $company)
    {
        return $this->invitations()->where('company_id', $company->id)->whereNotNull('completed_at')->count() > 0;
    }

    public function getCampaignsForCompany(Company $company)
    {
        return $this->campaigns()
            ->where(function ($query) use ($company) {
                $query
                    ->where('campaigns.agency_id', $company->id)
                    ->orWhere('campaigns.dealership_id', $company->id);
            })
            ->get();
    }

    public function hasAccessToCampaign(int $campaignId)
    {
        $company = $this->campaigns()->find($campaignId);
        if (empty($company)) {
            return false;
        }
        return true;
    }

    public function getPossibleTimezones()
    {
        return self::getPossibleTimezonesForUser();
    }

    static function getPossibleTimezonesForUser()
    {
        return [
            'US/Alaska',
            'US/Aleutian',
            'US/Arizona',
            'US/Central',
            'US/East-Indiana',
            'US/Eastern',
            'US/Hawaii',
            'US/Indiana-Starke',
            'US/Michigan',
            'US/Mountain',
            'US/Pacific',
            'US/Pacific-New',
            'US/Samoa',
        ];
    }

    public static function searchByRequest(Request $request)
    {
        $loggedUser = auth()->user();
        $query = self::query();
        if ($request->has('company') && $loggedUser->isAdmin()) {
            $query->filterByCompany(Company::findOrFail($request->input('company')));
        } else if (!$loggedUser->isAdmin() && $loggedUser->isCompanyAdmin(get_active_company())) {
            $query->filterByCompany(Company::findOrFail(get_active_company()));
        }
        if ($request->has('q')) {
            $query->filterByQuery($request->input('q'));
        }
        return $query;
    }

    public function scopeFilterByCompany($query, Company $company)
    {
        return $query->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->where('company_id', $company->id);
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }
}
