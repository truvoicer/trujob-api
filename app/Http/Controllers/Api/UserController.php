<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\PersonalAccessTokenResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class UserController extends Controller
{
    public function __construct(
    ) {
    }

    public function getSessionUserDetail(Request $request)
    {
        return $this->sendSuccessResponse(
            "success",
            new UserResource($request->user())
        );
    }


    public function getSessionUserApiToken(Request $request)
    {
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource($request->user()->currentAccessToken())
        );
    }

    public function getSessionUserApiTokenList(Request $request)
    {
        $getApiTokens = $this->userAdminService->findApiTokensByParams(
            $request->user(),
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            $request->get('count')
        );
        return PersonalAccessTokenResource::collection($getApiTokens);
    }

    public function generateSessionUserApiToken(Request $request)
    {
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource(
                $this->userAdminService->createUserToken(
                    $request->user(),
                )
            )
        );
    }

    public function deleteSessionUserApiToken(PersonalAccessToken $personalAccessToken, Request $request)
    {
        $delete = $this->userAdminService->deleteApiToken($personalAccessToken);
        if (!$delete) {
            return $this->sendErrorResponse(
                "Error deleting api token",
            );
        }
        return $this->sendSuccessResponse(
            "Api Token deleted.",
        );
    }

    public function updateSessionUser(UpdateUserRequest $request)
    {
        $this->accessControlService->setUser($request->user());
        $roles = [];
        if (
            $this->accessControlService->inAdminGroup() &&
            $request->has('role_id')
        ) {
            $roles = $request->get('roles');
        }
        $update = $this->userAdminService->updateUser(
            $request->user(),
            $request->all(),
            $roles
        );
        if (!$update) {
            return $this->sendErrorResponse("Error updating user");
        }
        return $this->sendSuccessResponse(
            "User updated",
            new UserResource(
                $this->userAdminService->getUserRepository()->getModel()
            )
        );
    }
}
