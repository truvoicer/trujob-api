<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRoleRequest;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\User\RoleService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService, Request $request)
    {
        $this->roleService = $roleService;
    }

    public function updateUserRole(User $user, Role $role, Request $request) {
        $this->roleService->setUser($user);
        $this->roleService->setRole($role);
        $create = $this->roleService->updateRole($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating role',
                [],
                $this->roleService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('role created', [], $this->roleService->getResultsService()->getErrors());
    }
    public function createRole(Request $request) {
        $this->roleService->setUser($request->user());
        $create = $this->roleService->createrole($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating role',
                [],
                $this->roleService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('role created', [], $this->roleService->getResultsService()->getErrors());
    }

    public function updateRole(role $role, Request $request) {
        $this->roleService->setUser($request->user());
        $this->roleService->setrole($role);
        $update = $this->roleService->updaterole($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating role',
                [],
                $this->roleService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('role updated', [], $this->roleService->getResultsService()->getErrors());
    }
    public function deleteRole(role $role) {
        $this->roleService->setrole($role);
        $delete = $this->roleService->deleterole();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting role',
                [],
                $this->roleService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('role deleted', [], $this->roleService->getResultsService()->getErrors());
    }

}
