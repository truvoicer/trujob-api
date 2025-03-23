<?php

namespace App\Services\Admin;

use App\Enums\Auth\ApiAbility;

class AuthService
{

    const API_ABILITIES = [
        [
            'name' => ApiAbility::SUPERUSER->value,
            'label' => 'Superuser',
            'ability' => 'api:superuser'
        ],
        [
            'name' => ApiAbility::PUBLIC->value,
            'label' => 'Public',
            'ability' => 'api:public'
        ],
        [
            'name' => ApiAbility::ADMIN->value,
            'label' => 'Admin',
            'ability' => 'api:admin'
        ],
        [
            'name' => ApiAbility::APP_USER->value,
            'label' => 'App User',
            'ability' => 'api:app_user'
        ],
        [
            'name' => ApiAbility::USER->value,
            'label' => 'User',
            'ability' => 'api:user'
        ],
        [
            'name' => ApiAbility::SITE->value,
            'label' => 'Site',
            'ability' => 'api:site'
        ],
    ];

    public static function getApiAbility(string $name)
    {
        $findAbilityIndex = array_search($name, array_column(self::API_ABILITIES, 'name'));
        if ($findAbilityIndex === false) {
            return false;
        }
        return self::API_ABILITIES[$findAbilityIndex]['ability'];
    }

    public static function getApiAbilityData(string $name)
    {
        $findAbilityIndex = array_search($name, array_column(self::API_ABILITIES, 'name'));
        if ($findAbilityIndex === false) {
            return false;
        }
        return self::API_ABILITIES[$findAbilityIndex];
    }
}
