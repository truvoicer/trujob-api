<?php

namespace App\Traits\Model;

use App\Enums\Auth\ApiAbility;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait PermissionTrait
{

    public function hasPermission(Site $site, Collection $roles, ?User $user = null): bool
    {
        if ($roles->isEmpty()) {
            return true;
        }
        
        $siteRoleExists = $roles->contains(function ($role){
            return $role instanceof Role && $role->name === ApiAbility::SITE->value;
        });
        if ($siteRoleExists) {
            return true;
        }
        if ($user instanceof User) {
            $userRolesContainAdmin = $user->roles->contains(function ($userRole) {
                return $userRole instanceof Role && (
                    $userRole->name === ApiAbility::ADMIN->value
                    || $userRole->name === ApiAbility::SUPERUSER->value
                );
            });
            
            if ($userRolesContainAdmin) {
                return true;
            }
            foreach ($roles as $role) {
                $userRoleContains = $user->roles->contains(function ($userRole) use ($role) {
                    return $userRole instanceof Role && $userRole->id === $role->id;
                });
                if ($userRoleContains) {
                    return true;
                }
            }
        }
        return false;
    }
}
