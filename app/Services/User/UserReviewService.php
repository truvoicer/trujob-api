<?php

namespace App\Services\User;

use App\Models\UserReview;
use App\Models\User;
use Illuminate\Http\Request;

class UserReviewService
{

    private User $user;
    private Request $request;
    private UserReview $userReview;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createUserReview(array $data) {
        $this->userReview = new UserReview($data);
        $createUserReview = $this->user->userReview()->save($this->userReview);
        if (!$createUserReview) {
            $this->addError('Error creating user review for user', $data);
            return false;
        }
        return true;
    }

    public function updateUserReview(array $data) {
        $this->userReview->fill($data);
        $saveUserReview = $this->userReview->save();
        if (!$saveUserReview) {
            $this->addError('Error saving user review', $data);
            return false;
        }
        return true;
    }

    public function deleteUserReview() {
        if (!$this->userReview->delete()) {
            $this->addError('Error deleting user review');
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
     * @param UserReview $userReview
     */
    public function setUserReview(UserReview $userReview): void
    {
        $this->userReview = $userReview;
    }

    /**
     * @return UserReview
     */
    public function getUserReview(): UserReview
    {
        return $this->userReview;
    }


}
