<?php

namespace App\Services\User;

use App\Models\UserFollow;
use App\Models\User;
use Illuminate\Http\Request;

class UserFollowService
{

    private User $user;
    private Request $request;
    private UserFollow $userFollow;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createUserFollow(array $data) {
        $this->userFollow = new UserFollow($data);
        $createUser = $this->user->userFollow()->save($this->userFollow);
        if (!$createUser) {
            $this->addError('Error creating user follow for user', $data);
            return false;
        }
        return true;
    }

    public function updateUserFollow(array $data) {
        $this->userFollow->fill($data);
        $saveUserFollow = $this->userFollow->save();
        if (!$saveUserFollow) {
            $this->addError('Error saving user follow', $data);
            return false;
        }
        return true;
    }

    public function deleteUserFollow() {
        if (!$this->userFollow->delete()) {
            $this->addError('Error deleting user follow');
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
     * @param UserFollow $userFollow
     */
    public function setUserFollow(UserFollow $userFollow): void
    {
        $this->userFollow = $userFollow;
    }

    /**
     * @return UserFollow
     */
    public function getUserFollow(): UserFollow
    {
        return $this->userFollow;
    }


}
