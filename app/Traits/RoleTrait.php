<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleTrait
{

    public function syncRoles(BelongsToMany $roles, array $roleIds): array
    {
        return $roles->sync($roleIds);
    }

}