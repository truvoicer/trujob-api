<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserSettingRequest;
use App\Http\Requests\User\UpdateUserSettingRequest;
use App\Models\UserSetting;
use App\Services\User\UserSettingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSettingController extends Controller
{
    protected UserSettingService $userSettingService;

    public function __construct(UserSettingService $userSettingService, Request $request)
    {
        $this->userSettingService = $userSettingService;
    }


    public function createUserSetting(StoreUserSettingRequest $request) {
        $this->userSettingService->setUser($request->user());
        $createUserSetting = $this->userSettingService->createUserSetting($request->all());
        if (!$createUserSetting) {
            return $this->sendErrorResponse(
                'Error creating user setting',
                [],
                $this->userSettingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User setting created', [], $this->userSettingService->getErrors());
    }

    public function updateUserSetting(UserSetting $userSetting, Request $request) {
        $this->userSettingService->setUser($request->user());
        $this->userSettingService->setUserSetting($userSetting);
        $createUserSetting = $this->userSettingService->updateUserSetting($request->all());
        if (!$createUserSetting) {
            return $this->sendErrorResponse(
                'Error updating User setting',
                [],
                $this->userSettingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User setting updated', [], $this->userSettingService->getErrors());
    }
    public function deleteUserSetting(UserSetting $userSetting) {
        $this->userSettingService->setUserSetting($userSetting);
        $deleteUserSetting = $this->userSettingService->deleteUserSetting();
        if (!$deleteUserSetting) {
            return $this->sendErrorResponse(
                'Error deleting user setting',
                [],
                $this->userSettingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User setting deleted', [], $this->userSettingService->getErrors());
    }
}
