<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleTrait
{

    public function syncRoles(BelongsToMany $roles, array $roleData): array
    {
        if (
            count(array_filter($roleData, fn($roleId) => is_numeric($roleId))) === count($roleData)
        ) {
            return $roles->sync($roleData);
        }


        if (
            count(array_filter($roleData, fn($roleId) => is_string($roleId))) === count($roleData)
        ) {
            $roleIds = array_map(
                fn($role) => Role::where('name', $role)->first()?->id,
                $roleData
            );
            return $roles->sync($roleIds);
        }

        throw new \Exception("Error syncing roles");
    }
}
