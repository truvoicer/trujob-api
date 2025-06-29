<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\BaseService;

class UserSettingService extends BaseService
{

    public function updateUserSetting(array $data) {
        $createUserSetting = $this->user->userSetting()->updateOrCreate([
            'user_id' => $this->user->id,
        ], $data);
        if (!$createUserSetting) {
            throw new \Exception('Error creating user setting for user');
        }
        return true;
    }

}
