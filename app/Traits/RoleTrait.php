<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleTrait
{
    public function buildRoleIds(array $roleData): array
    {
        if (
            count(array_filter($roleData, fn($roleId) => is_numeric($roleId))) === count($roleData)
        ) {
            return $roleData;
        }

        if (
            count(array_filter($roleData, fn($roleId) => is_string($roleId))) === count($roleData)
        ) {
            return array_map(
                fn($role) => Role::where('name', $role)->first()?->id,
                $roleData
            );
        }

        throw new \Exception("Error building role ids");
    }

    public function syncRoles(BelongsToMany $roles, array $roleData): array
    {
        return $roles->sync(
            $this->buildRoleIds($roleData)
        );
    }

    public function assignRoles(BelongsToMany $roles, array $roleData): void
    {
        $roles->attach(
            $this->buildRoleIds($roleData)
        );
    }

}
