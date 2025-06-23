<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserSettingRequest;
use App\Http\Resources\User\UserSettingResource;
use App\Services\User\UserSettingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSettingController extends Controller
{

    public function __construct(
        private UserSettingService $userSettingService
    ){}

    public function show(Request $request) {
        $this->userSettingService->setUser($request->user()->user);
        $this->userSettingService->setSite($request->user()->site);
        $userSetting = $request->user()->user->setting;
        if (!$userSetting) {
            return response()->json([
                'message' => 'User setting not found',
            ], Response::HTTP_NOT_FOUND);
        }
        return UserSettingResource::make($userSetting);
    }

    public function update(UpdateUserSettingRequest $request) {
        $this->userSettingService->setUser($request->user()->user);
        $this->userSettingService->setSite($request->user()->site);
        $createUserSetting = $this->userSettingService->updateUserSetting($request->validated());
        if (!$createUserSetting) {
            return response()->json([
                'message' => 'Error creating user setting for user',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'User setting created',
        ], Response::HTTP_OK);
    }

}
