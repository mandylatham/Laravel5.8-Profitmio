<?php

namespace App\Models\Imersonation\Traits;

use App\Models\Impersonation\ImpersonatedAction;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * This trait can be added to any model
 * that may be used by an impersonated user
 *
 * @package App\Models\Imersonation\Traits
 */
trait CanImpersonate
{
    public function impersonation(): MorphOne
    {
        return $this->morphOne(ImpersonatedAction::class, 'object')
            ->where('action', ImpersonatedAction::TYPE_CREATE);
    }
}
