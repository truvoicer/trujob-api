<?php
namespace App\Enums\Auth;

enum ApiAbility: string
{
    case SUPERUSER = 'superuser';
    case ADMIN = 'admin';
    case APP_USER = 'app_user';
    case USER = 'user';
    case PUBLIC = 'public';
    case SITE = 'site';
}
