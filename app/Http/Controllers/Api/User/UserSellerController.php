<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserSellerRequest;
use App\Http\Requests\User\UpdateUserSellerRequest;
use App\Models\User;
use App\Models\UserSeller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSellerController extends Controller
{

    public function addUserSeller(User $user, Request $request) {
        $this->userAdminService->setUser($user);
        if (!$this->userAdminService->addUserSeller()) {
            return $this->sendErrorResponse(
                'Error adding user as seller',
                [],
                $this->userAdminService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User added as a seller', [], $this->userAdminService->getResultsService()->getErrors());

    }

    public function removeUserSeller(User $user, Request $request) {
        $this->userAdminService->setUser($user);
        if (!$this->userAdminService->addUserSeller()) {
            return $this->sendErrorResponse(
                'Error removing user as a seller',
                [],
                $this->userAdminService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('User removed as seller', [], $this->userAdminService->getResultsService()->getErrors());

    }
}
