<?php

namespace App\Models\Impersonation\Traits;

use App\Models\Impersonation\ImpersonatedAction;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * This trait can be added to any model
 * that may be used by an impersonated user
 *
 * @package App\Models\Impersonation\Traits
 */
trait MayBeImpersonated
{
    public function impersonation(): MorphOne
    {
        return $this->morphOne(ImpersonatedAction::class, 'object')
            ->where('action', ImpersonatedAction::TYPE_CREATE);
    }
}
