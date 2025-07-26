<?php

namespace App\Enums\PersonalAccessToken;

enum PersonalAccessTokenableType: string
{
    case USER = 'user';
    case SITE = 'site';
    case SITE_USER = 'site_user';
}
