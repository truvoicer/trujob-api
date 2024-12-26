<?php

namespace App\Services\Admin;

class AuthService
{
    const ABILITY_SUPERUSER = 'superuser';
    const ABILITY_PUBLIC = 'public';
    const ABILITY_ADMIN = 'admin';
    const ABILITY_APP_USER = 'app_user';
    const API_ABILITIES = [
        [
            'name' => 'superuser',
            'label' => 'Superuser',
            'ability' => 'api:superuser'
        ],
        [
            'name' => 'public',
            'label' => 'Public',
            'ability' => 'api:public'
        ],
        [
            'name' => 'admin',
            'label' => 'Admin',
            'ability' => 'api:admin'
        ],
        [
            'name' => 'app_user',
            'label' => 'App User',
            'ability' => 'api:app_user'
        ]
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
