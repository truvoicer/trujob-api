<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserFollowRequest;
use App\Http\Requests\User\UpdateUserFollowRequest;
use App\Models\UserFollow;
use App\Services\User\UserFollowService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFollowController extends Controller
{
    protected UserFollowService $userFollowService;

    public function __construct(UserFollowService $userFollowService, Request $request)
    {
        $this->userFollowService = $userFollowService;
    }


    public function createUserFollow(StoreUserFollowRequest $request) {
        $this->userFollowService->setUser($request->user());
        $createUserFollow = $this->userFollowService->createUserFollow($request->all());
        if (!$createUserFollow) {
            return $this->sendErrorResponse(
                'Error creating user follow',
                [],
                $this->userFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User follow created', [], $this->userFollowService->getErrors());
    }

    public function updateUserFollow(UserFollow $userFollow, Request $request) {
        $this->userFollowService->setUser($request->user());
        $this->userFollowService->setUserFollow($userFollow);
        $createUserFollow = $this->userFollowService->updateUserFollow($request->all());
        if (!$createUserFollow) {
            return $this->sendErrorResponse(
                'Error updating User follow',
                [],
                $this->userFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User follow updated', [], $this->userFollowService->getErrors());
    }
    public function deleteUserFollow(UserFollow $userFollow) {
        $this->userFollowService->setUserFollow($userFollow);
        $deleteUserFollow = $this->userFollowService->deleteUserFollow();
        if (!$deleteUserFollow) {
            return $this->sendErrorResponse(
                'Error deleting user follow',
                [],
                $this->userFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User follow deleted', [], $this->userFollowService->getErrors());
    }
}
