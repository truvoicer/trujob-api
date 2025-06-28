<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Services\User\UserProfileService;
use App\Services\User\UserSettingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController extends Controller
{

    public function __construct(
        private UserProfileService $userProfileService,
        private UserSettingService $userSettingService
    ) {}

    public function update(UpdateUserProfileRequest $request)
    {
        $this->userProfileService->setUser($request->user()->user);
        $this->userProfileService->setSite($request->user()->site);

        $this->userSettingService->setUser($request->user()->user);
        $this->userSettingService->setSite($request->user()->site);

        $createUserProfile = $this->userProfileService->updateUserProfile($request->validated());
        if (!$createUserProfile) {
            return response()->json([
                'message' => 'Error updating user profile',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userSettings = [];
        if ($request->has('country_id')) {
            $userSettings['country_id'] = $request->validated('country_id');
        }
        if ($request->has('language_id')) {
            $userSettings['language_id'] = $request->validated('language_id');
        }
        if ($request->has('currency_id')) {
            $userSettings['currency_id'] = $request->validated('currency_id');
        }
        $createUserProfile = $this->userSettingService->updateUserSetting($userSettings);
        if (!$createUserProfile) {
            return response()->json([
                'message' => 'Error updating user profile',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'User profile updated successfully',
        ], Response::HTTP_OK);
    }
}
