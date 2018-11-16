<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;

    const TYPE_SUPPORT = 'support';
    const TYPE_AGENCY = 'agency';
    const TYPE_DEALERSHIP = 'dealership';

    protected $fillable = ['name', 'type'];

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

    public function getCampaigns()
    {
        return Campaign::where(function ($query) {
            $query->where('agency_id', $this->id)
                ->orWhere('dealership_id', $this->id);
        })->get();
    }
}
