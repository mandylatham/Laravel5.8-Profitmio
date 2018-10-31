<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['agency_id', 'dealership_id', 'name'];

    public function agency()
    {
        return $this->hasOne(Company::class, 'id', 'agency_id');
    }

    public function dealership()
    {
        return $this->hasOne(Company::class, 'id', 'dealership_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getCompanyCampaigns(int $companyId)
    {
        return
            self::whereNull('deleted_at')
                ->where(function($query) use ($companyId) {
                    $query->where('agency_id', $companyId)
                        ->orWhere('dealership_id', $companyId);
                })->get();
    }
}
