<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Permission\PermissionEntities;
use App\Traits\Error\ErrorTrait;
use App\Traits\SiteTrait;
use App\Traits\User\UserTrait;

class BaseService
{
    use UserTrait, ErrorTrait, SiteTrait;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }


}
