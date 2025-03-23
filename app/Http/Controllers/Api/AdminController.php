<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Requests\Admin\User\DeleteBatchUserRequest;
use App\Http\Requests\Auth\GenerateApiTokenRequest;
use App\Http\Resources\PersonalAccessTokenResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Permission\AccessControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class AdminController extends Controller
{

    /**
     * Gets a list of users based on the request query data
     * Returns successful json response and array of user objects
     *
     */
    public function getUsersList(Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        $getUsers = $this->userAdminService->findByParams(
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            $request->get('count', -1)
        );
        return UserResource::collection($getUsers);
    }
    public function getUserRoleList(Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        return $this->sendSuccessResponse(
            "success",
            RoleResource::collection(
                $this->userAdminService->findUserRoles(
                    $request->get('sort', "id"),
                    $request->get('order', "asc"),
                    $request->get('count', -1)
                )
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     */
    public function getSingleUser(User $user, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        return $this->sendSuccessResponse(
            "success",
            new UserResource($user)
        );
    }

    /**
     * Creates a user based on the request post data
     *
     */
    public function createUser(CreateUserRequest $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        $requestData = $request->all([
            'email',
            'password',
        ]);

        $create = $this->userAdminService->createUserByRoleId($requestData, $request->get('roles'));

        if (!$create) {
            return $this->sendErrorResponse("Error inserting user");
        }
        return $this->sendSuccessResponse(
            "User inserted",
            new UserResource(
                $this->userAdminService->getUserRepository()->getModel()
            )
        );
    }

    /**
     * Updates a user based on the post request data
     *
     */
    public function updateUser(User $user, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        $update = $this->userAdminService->updateUser(
            $user,
            $request->all(),
            $request->get('roles', [])
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

    /**
     * Deletes a user based on the post request data
     *
     */
    public function deleteUser(User $user, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        if (!$this->userAdminService->deleteUser($user)) {
            return $this->sendErrorResponse(
                "Error deleting user",
            );
        }
        return $this->sendSuccessResponse("User deleted.");
    }

    /**
     * Gets a single api token based on the api token id in the request url
     *
     */
    public function getApiToken(PersonalAccessToken $apiToken, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource($apiToken)
        );
    }

    /**
     * Gets a list of usee api tokens based on the user id in the request url
     *
     */
    public function getUserApiTokens(User $user, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        $getApiTokens = $this->userAdminService->findApiTokensByParams(
            $user,
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            $request->get('count')
        );
        return PersonalAccessTokenResource::collection($getApiTokens);
    }

    /**
     * Generates a new api token for a single user
     * User is based on the id in the request url
     *
     */
    public function generateNewApiToken(User $user, GenerateApiTokenRequest $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        $generateToken = $this->userAdminService->createUserTokenByRoleId(
            $user,
            $request->validated('role_id'),
            $request->validated('expires_at', null),
        );
        if (!$generateToken) {
            return $this->sendErrorResponse(
                "Error generating api token",
            );
        }
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource (
                $generateToken
            )
        );
    }

    /**
     * Updates a single token expiry date based on the request post data
     *
     */
    public function updateApiTokenExpiry(User $user, PersonalAccessToken $personalAccessToken, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
        if (!$this->userAdminService->updateApiTokenExpiry($personalAccessToken, $request->all())) {
            return $this->sendErrorResponse(
                "Error updating api token"
            );
        }
        return $this->sendSuccessResponse(
            "Successfully updated token.",
            new PersonalAccessTokenResource(
                $this->userAdminService->getPersonalAccessTokenRepository()->getModel()
            )
        );
    }

    /**
     * Delete a single api token based the request post data.
     *
     */
    public function deleteApiToken(User $user, PersonalAccessToken $personalAccessToken, Request $request)
    {
        $this->accessControlService->setUser($request->user());
        if (!$this->accessControlService->inAdminGroup()) {
            return $this->sendErrorResponse("Access control: operation not permitted");
        }
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

    public function deleteBatchUser(
        DeleteBatchUserRequest $request
    ): \Illuminate\Http\JsonResponse
    {
        $this->setAccessControlUser($request->user());

        if (!$this->userAdminService->deleteBatchUser($request->get('ids'))) {
            return $this->sendErrorResponse(
                "Error deleting users",
            );
        }
        return $this->sendSuccessResponse(
            "Users deleted.",
        );
    }
}
