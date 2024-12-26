<?php

namespace App\Services\User;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;

class UserProfileService
{

    private User $user;
    private Request $request;
    private UserProfile $userProfile;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createUserProfile(array $data) {
        $this->userProfile = new UserProfile($data);
        $createUserProfile = $this->user->userProfile()->save($this->userProfile);
        if (!$createUserProfile) {
            $this->addError('Error creating user profile for user', $data);
            return false;
        }
        return true;
    }

    public function updateUserProfile(array $data) {
        $this->userProfile->fill($data);
        $saveUserProfile = $this->userProfile->save();
        if (!$saveUserProfile) {
            $this->addError('Error saving user profile', $data);
            return false;
        }
        return true;
    }

    public function deleteUserProfile() {
        if (!$this->userProfile->delete()) {
            $this->addError('Error deleting user profile');
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
     * @param UserProfile $userProfile
     */
    public function setUserProfile(UserProfile $userProfile): void
    {
        $this->userProfile = $userProfile;
    }

    /**
     * @return UserProfile
     */
    public function getUserProfile(): UserProfile
    {
        return $this->userProfile;
    }


}
