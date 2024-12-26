<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRewardRequest;
use App\Http\Requests\User\UpdateUserRewardRequest;
use App\Models\UserReward;
use App\Services\User\UserRewardService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRewardController extends Controller
{
    protected UserRewardService $userRewardService;

    public function __construct(UserRewardService $userRewardService, Request $request)
    {
        $this->userRewardService = $userRewardService;
    }


    public function createUserReward(StoreUserRewardRequest $request) {
        $this->userRewardService->setUser($request->user());
        $createUserReward = $this->userRewardService->createUserReward($request->all());
        if (!$createUserReward) {
            return $this->sendErrorResponse(
                'Error creating user reward',
                [],
                $this->userRewardService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User reward created', [], $this->userRewardService->getErrors());
    }

    public function updateUserReward(UserReward $userReward, Request $request) {
        $this->userRewardService->setUser($request->user());
        $this->userRewardService->setUserReward($userReward);
        $createUserReward = $this->userRewardService->updateUserReward($request->all());
        if (!$createUserReward) {
            return $this->sendErrorResponse(
                'Error updating User reward',
                [],
                $this->userRewardService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User reward updated', [], $this->userRewardService->getErrors());
    }
    public function deleteUserReward(UserReward $userReward) {
        $this->userRewardService->setUserReward($userReward);
        $deleteUserReward = $this->userRewardService->deleteUserReward();
        if (!$deleteUserReward) {
            return $this->sendErrorResponse(
                'Error deleting user reward',
                [],
                $this->userRewardService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User reward deleted', [], $this->userRewardService->getErrors());
    }
}
