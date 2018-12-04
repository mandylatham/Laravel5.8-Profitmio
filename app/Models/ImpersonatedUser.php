<?php

namespace App\Models;

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
}
