<?php

namespace App\Classes;

use App\CompanyUser;
use App\User;

class CompanyUserActivityLog
{
    private const MESSAGE_ATTACHED = 'User %d assigned to company %d with role %s';
    private const MESSAGE_DETACHED = 'User %d de-assigned from company %d';
    private const MESSAGE_UPDATED = 'Role for user %d in company %d changed from %s to %s';
    private const MESSAGE_UPDATED_PREFERENCES = 'User %d set timezone %s for company %d';

    public function attach(User $user, int $companyId, string $role): void
    {
        $properties = [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'role' => $role,
        ];
        $pivot = new CompanyUser();
        $pivot->id = $user->id;
        $logMessage = sprintf(self::MESSAGE_ATTACHED, $user->id, $companyId, $role);
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
        $logMessage = sprintf(self::MESSAGE_DETACHED, $user->id, $companyId);
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
        $logMessage = sprintf(self::MESSAGE_UPDATED, $user->id, $companyId, $oldRole, $newRole);
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
        $logMessage = sprintf(self::MESSAGE_UPDATED_PREFERENCES, $user->id, $attributes['config']['timezone'], $companyId);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }
}
