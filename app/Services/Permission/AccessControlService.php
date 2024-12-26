<?php

namespace App\Services\Permission;

use App\Services\Auth\AuthService;
use App\Services\User\UserAdminService;
use App\Traits\User\UserTrait;

class AccessControlService
{
    use UserTrait;

    public function inAdminGroup(): bool
    {
        $user = $this->getUser();
        return (
            UserAdminService::userTokenHasAbility($user, AuthService::ABILITY_SUPERUSER)
        );
    }

}
