<?php

namespace App\Services\Auth;

use App\Enums\Auth\ApiAbility;
use App\Helpers\Db\DbHelpers;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;

class AuthService
{
    const DEFAULT_ROLES = [
        [
            'name' => ApiAbility::SUPERUSER->value,
            'label' => 'Super User',
            'ability' => 'api:superuser',
            'available_roles' => [
                ApiAbility::ADMIN->value,
                ApiAbility::USER->value,
                ApiAbility::APP_USER->value
            ]
        ],
        [
            'name' => ApiAbility::ADMIN->value,
            'label' => 'Admin',
            'ability' => 'api:admin',
            'available_roles' => [
                ApiAbility::USER->value,
                ApiAbility::APP_USER->value
            ]
        ],
        [
            'name' => ApiAbility::USER->value,
            'label' => 'User',
            'ability' => 'api:user',
            'available_roles' => [
                ApiAbility::APP_USER->value
            ]
        ],
        [
            'name' => ApiAbility::APP_USER->value,
            'label' => 'App User',
            'ability' => 'api:app_user',
            'available_roles' => []
        ],
        [
            'name' => ApiAbility::SITE->value,
            'label' => 'Site',
            'ability' => 'api:site',
            'available_roles' => []
        ],
    ];

    public function __construct(
        private RoleRepository $roleRepository
    )
    {
        $this->roleRepository = new RoleRepository();
    }

    public static function getApiAbility(string $name)
    {
        $findAbilityIndex = array_search($name, array_column(self::DEFAULT_ROLES, 'name'));
        if ($findAbilityIndex === false) {
            return false;
        }
        return self::DEFAULT_ROLES[$findAbilityIndex]['ability'];
    }

    public static function getApiAbilityData(string $name)
    {
        $findAbilityIndex = array_search($name, array_column(self::DEFAULT_ROLES, 'name'));
        if ($findAbilityIndex === false) {
            return false;
        }
        return self::DEFAULT_ROLES[$findAbilityIndex];
    }

    public function fetchAvailableRoles(Collection $roles)
    {
        $names = DbHelpers::pluckByColumn($roles, 'name');
        $filter = array_filter(self::DEFAULT_ROLES, function ($role) use ($names) {
            return in_array($role['name'], $names);
        });

        $availableRolesNames = array_unique(
            array_merge(
                $names,
                ...array_column($filter, 'available_roles')
            )
        );
        return $this->roleRepository->fetchRolesByNames($availableRolesNames);
    }

    public function getRoles(array $roleIds): Collection
    {
        $fetchRoles = $this->roleRepository->fetchRolesById($roleIds);
        return $this->fetchAvailableRoles($fetchRoles);
    }
    public function getRoleIds(array $roleIds): array
    {
        return DbHelpers::pluckByColumn(
            $this->getRoles($roleIds),
            'id'
        );
    }
}
