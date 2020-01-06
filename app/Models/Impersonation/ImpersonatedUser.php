<?php

namespace App\Models\Impersonation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ImpersonatedUser extends Model
{
    protected $fillable = ['user_id', 'impersonator_id'];

    public static function findOrCreateImpersonatedUser(int $userId, int $impersonatorId)
    {
        $impersonated = self::where('user_id', $userId)->where('impersonator_id', $impersonatorId)->first();
        if (empty($impersonated)) {
            $impersonated = self::create(
                [
                    'user_id' => $userId,
                    'impersonator_id' => $impersonatorId
                ]
            );
        }
        return $impersonated;
    }

    public function impersonatorUser()
    {
        return $this->belongsTo(User::class, 'impersonator_id', 'id');
    }

    public function impersonatedUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
