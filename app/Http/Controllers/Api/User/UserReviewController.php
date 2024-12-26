<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserReviewRequest;
use App\Http\Requests\User\UpdateUserReviewRequest;
use App\Models\UserReview;
use App\Services\User\UserReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserReviewController extends Controller
{
    protected UserReviewService $userReviewService;

    public function __construct(UserReviewService $userReviewService, Request $request)
    {
        $this->userReviewService = $userReviewService;
    }


    public function createUserReview(StoreUserReviewRequest $request) {
        $this->userReviewService->setUser($request->user());
        $createUserReview = $this->userReviewService->createUserReview($request->all());
        if (!$createUserReview) {
            return $this->sendErrorResponse(
                'Error creating user review',
                [],
                $this->userReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User review created', [], $this->userReviewService->getErrors());
    }

    public function updateUserReview(UserReview $userReview, Request $request) {
        $this->userReviewService->setUser($request->user());
        $this->userReviewService->setUserReview($userReview);
        $createUserReview = $this->userReviewService->updateUserReview($request->all());
        if (!$createUserReview) {
            return $this->sendErrorResponse(
                'Error updating User review',
                [],
                $this->userReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User review updated', [], $this->userReviewService->getErrors());
    }
    public function deleteUserReview(UserReview $userReview) {
        $this->userReviewService->setUserReview($userReview);
        $deleteUserReview = $this->userReviewService->deleteUserReview();
        if (!$deleteUserReview) {
            return $this->sendErrorResponse(
                'Error deleting user review',
                [],
                $this->userReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User review deleted', [], $this->userReviewService->getErrors());
    }
}
