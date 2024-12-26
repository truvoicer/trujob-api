<?php

namespace App\Repositories;

use App\Models\UserSetting;

class UserSettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserSetting::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserSetting
    {
        return parent::getModel();
    }

}
