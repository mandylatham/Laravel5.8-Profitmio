<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;

class CompanyUserActivityLog
{
    private const MESSAGE_ATTACHED = 'User %d (%s) assigned to company %d (%s) with role %s';
    private const MESSAGE_DETACHED = 'User %d (%s) de-assigned from company %d (%s)';
    private const MESSAGE_UPDATED = 'Role for user %d (%s) in company %d (%s) changed from %s to %s';
    private const MESSAGE_UPDATED_PREFERENCES = 'User %d (%s) set timezone %s for company %d(%s) ';

    public function attach(User $user, int $companyId, string $role): void
    {
        $properties = [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'role' => $role,
        ];
        $pivot = new CompanyUser();
        $pivot->id = $user->id;
        $company = Company::find($companyId);
        $logMessage = sprintf(self::MESSAGE_ATTACHED, $user->id, $user->name, $companyId, $company->name, $role);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }

    public function detach(User $user, int $companyId): void
    {
        $properties = [
            'user_id' => $user->id,
            'company_id' => $companyId,
        ];
        $pivot = new CompanyUser();
        $pivot->id = $user->id;
        $company = Company::find($companyId);
        $logMessage = sprintf(self::MESSAGE_DETACHED, $user->id, $user->name, $companyId, $company->name);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }

    public function update(User $user, int $companyId, string $oldRole, string $newRole): void
    {
        $properties = [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'old_role' => $oldRole,
            'new_role' => $newRole,
        ];
        $pivot = new CompanyUser();
        $pivot->id = $user->id;
        $company = Company::find($companyId);
        $logMessage = sprintf(self::MESSAGE_UPDATED, $user->id, $user->name, $companyId, $company->name, $oldRole, $newRole);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }

    public function sync(User $user, array $changes, array $permissions, array $oldPermissions): void
    {
        foreach($changes['attached'] as $companyId) {
            $this->attach($user, $companyId, $permissions[$companyId]['role']);
        }
        foreach($changes['detached'] as $companyId) {
            $this->detach($user, $companyId);
        }
        foreach($changes['updated'] as $companyId) {
            $this->update($user, $companyId, $oldPermissions[$companyId]['role'], $permissions[$companyId]['role']);
        }
    }

    public function updatePreferences(User $user, int $companyId, array $attributes)
    {
        $properties = [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'attributes' => $attributes,
        ];
        $pivot = new CompanyUser();
        $pivot->id = $user->id;
        $company = Company::find($companyId);
        $logMessage = sprintf(self::MESSAGE_UPDATED_PREFERENCES, $user->id, $user->name, $attributes['config']['timezone'], $companyId, $company->name);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }
}
