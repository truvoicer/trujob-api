<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\User;
use App\Services\Permission\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PermissionController extends Controller
{

    public function __construct(
        private PermissionService $permissionService
    )
    {
    }

    public function getPermissions(Request $request)
    {
        $getPermissions = $this->permissionService->findByParams(
            $request->get('sort', "name"),
            $request->get('order', "asc"),
            $request->get('count', -1)
        );
        return PermissionResource::collection($getPermissions);
    }

    public function getSinglePermission(Permission $permission)
    {
        return $this->sendSuccessResponse("success",
            new PermissionResource($permission)
        );
    }

    /**
     * Creates a new permission based on the request post data
     *
     * @param Request $request
     * @return PermissionResource|JsonResponse
     */
    public function createPermission(Request $request)
    {
        $create = $this->permissionService->createPermission($request->get('name'), $request->get('label'));
        if (!$create) {
            return $this->sendErrorResponse("Error creating permission.");
        }
        return new PermissionResource(
            $this->permissionService->getPermissionRepository()->getModel()
        );
    }

    /**
     * Updates a new permission based on request post data
     *
     * @param Permission $permission
     * @param Request $request
     * @return PermissionResource|JsonResponse
     */
    public function updatePermission(Permission $permission, Request $request)
    {
        $update = $this->permissionService->updatePermission($permission, $request->all());
        if (!$update) {
            return $this->sendErrorResponse("Error updating permission.");
        }

        return new PermissionResource(
            $this->permissionService->getPermissionRepository()->getModel()
        );
    }

    /**
     * Deletes a permission based on the request post data
     *
     * @param Permission $permission
     * @param Request $request
     * @return JsonResponse
     */
    public function deletePermission(Permission $permission, Request $request)
    {
        if (!$this->permissionService->deletePermission($permission)) {
            return $this->sendErrorResponse("Error deleting permission");
        }
        return $this->sendSuccessResponse("Permission deleted.");
    }
}
