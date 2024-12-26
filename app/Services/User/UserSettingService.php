<?php

namespace App\Services\User;

use App\Models\UserSetting;
use App\Models\User;
use Illuminate\Http\Request;

class UserSettingService
{

    private User $user;
    private Request $request;
    private UserSetting $userSetting;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createUserSetting(array $data) {
        $this->userSetting = new UserSetting($data);
        $createUserSetting = $this->user->userReward()->save($this->userSetting);
        if (!$createUserSetting) {
            $this->addError('Error creating user setting for user', $data);
            return false;
        }
        return true;
    }

    public function updateUserSetting(array $data) {
        $this->userSetting->fill($data);
        $saveUserSetting = $this->userSetting->save();
        if (!$saveUserSetting) {
            $this->addError('Error saving user setting', $data);
            return false;
        }
        return true;
    }

    public function deleteUserSetting() {
        if (!$this->userSetting->delete()) {
            $this->addError('Error deleting user setting');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param UserSetting $userSetting
     */
    public function setUserSetting(UserSetting $userSetting): void
    {
        $this->userSetting = $userSetting;
    }

    /**
     * @return UserSetting
     */
    public function getUserSetting(): UserSetting
    {
        return $this->userSetting;
    }


}
