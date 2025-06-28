<?php

namespace App\Services\User;

use App\Services\BaseService;

class UserProfileService extends BaseService
{

    public function updateUserProfile(array $data) {
        $createUserProfile = $this->user->userProfile()->updateOrCreate(
            ['user_id' => $this->user->id],
            $data
        );
        if (!$createUserProfile->exists()) {
            throw new \Exception('Error creating user profile');
        }
        return true;
    }

}
