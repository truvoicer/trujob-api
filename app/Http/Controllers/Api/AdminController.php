<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Requests\Admin\User\DeleteBatchUserRequest;
use App\Http\Requests\Auth\GenerateApiTokenRequest;
use App\Http\Resources\PersonalAccessTokenResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\User\UserResource;
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

}
