<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable, Impersonate, LogsActivity;

    protected static $logAttributes = ['id', 'name', 'is_admin', 'email', 'campaigns', 'companies'];

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'last_name',
        'email',
        'timezone',
        'phone_number',
        'password',
        'is_admin',
        'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function agencyCampaigns()
    {
        return $this->hasMany(Campaign::class, 'agency_id', 'id');
    }

    /**
     * Check if user belongs to given company
     * @param int $companyId
     * @return bool
     */
    public function belongsToCompany(int $companyId)
    {
        return $this->companies()->where('companies.id', $companyId)->count() === 1;
    }
    /**
     * The roles that belong to the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class)->using(CompanyUser::class)->withPivot('role', 'config', 'completed_at');
    }

    /**
     * The roles that belong to the user.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
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
        if (empty($company) || $company->pivot->role != self::ROLE_ADMIN) {
            return false;
        }
        return true;
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

    public function getCampaigns(int $companyId)
    {
        return DB::table('campaigns')
                 ->select('campaigns.*')
                 ->join('campaign_user', 'campaign_user.campaign_id', '=', 'campaigns.id')
                 ->where('campaign_user.user_id', $this->id)
                 ->where(function($query) use ($companyId) {
                        $query->where('campaigns.agency_id', $companyId)
                        ->orWhere('campaigns.dealership_id', $companyId);
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
}
