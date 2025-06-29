<?php

namespace App\Services\User;

use App\Services\BaseService;
use Illuminate\Support\Carbon;

class UserProfileService extends BaseService
{

    public function updateUserProfile(array $data) {
        if (!empty($data['dob']) && is_string($data['dob'])) {
            $data['dob'] = Carbon::parse($data['dob']);
        }
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
