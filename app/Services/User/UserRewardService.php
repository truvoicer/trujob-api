<?php

namespace App\Services\User;

use App\Models\UserReward;
use App\Models\User;
use Illuminate\Http\Request;

class UserRewardService
{

    private User $user;
    private Request $request;
    private UserReward $userReward;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createUserReward(array $data) {
        $this->userReward = new UserReward($data);
        $createUserReward = $this->user->userReward()->save($this->userReward);
        if (!$createUserReward) {
            $this->addError('Error creating user reward for user', $data);
            return false;
        }
        return true;
    }

    public function updateUserReward(array $data) {
        $this->userReward->fill($data);
        $saveUserReward = $this->userReward->save();
        if (!$saveUserReward) {
            $this->addError('Error saving user reward', $data);
            return false;
        }
        return true;
    }

    public function deleteUserReward() {
        if (!$this->userReward->delete()) {
            $this->addError('Error deleting user reward');
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
     * @param UserReward $userReward
     */
    public function setUserReward(UserReward $userReward): void
    {
        $this->userReward = $userReward;
    }

    /**
     * @return UserReward
     */
    public function getUserReward(): UserReward
    {
        return $this->userReward;
    }


}
