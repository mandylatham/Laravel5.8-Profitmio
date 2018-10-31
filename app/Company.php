<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    const TYPE_SUPPORT = 'support';
    const TYPE_AGENCY = 'agency';
    const TYPE_DEALERSHIP = 'dealership';

    protected $fillable = ['name', 'type'];

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
}
