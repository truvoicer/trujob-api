<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserProfileRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Models\UserProfile;
use App\Services\User\UserProfileService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController extends Controller
{
    protected UserProfileService $userProfileService;

    public function __construct(UserProfileService $userProfileService, Request $request)
    {
        $this->userProfileService = $userProfileService;
    }


    public function createUserProfile(StoreUserProfileRequest $request) {
        $this->userProfileService->setUser($request->user());
        $createUserProfile = $this->userProfileService->createUserProfile($request->all());
        if (!$createUserProfile) {
            return $this->sendErrorResponse(
                'Error creating user profile',
                [],
                $this->userProfileService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User profile created', [], $this->userProfileService->getErrors());
    }

    public function updateUserProfile(UserProfile $userProfile, Request $request) {
        $this->userProfileService->setUser($request->user());
        $this->userProfileService->setUserProfile($userProfile);
        $createUserProfile = $this->userProfileService->updateUserProfile($request->all());
        if (!$createUserProfile) {
            return $this->sendErrorResponse(
                'Error updating User profile',
                [],
                $this->userProfileService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User profile updated', [], $this->userProfileService->getErrors());
    }
    public function deleteUserProfile(UserProfile $userProfile) {
        $this->userProfileService->setUserProfile($userProfile);
        $deleteUserProfile = $this->userProfileService->deleteUserProfile();
        if (!$deleteUserProfile) {
            return $this->sendErrorResponse(
                'Error deleting user profile',
                [],
                $this->userProfileService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User profile deleted', [], $this->userProfileService->getErrors());
    }
}
