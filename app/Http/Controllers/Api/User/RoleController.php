<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRoleRequest;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Services\User\RoleService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    
    public function __construct(
        private RoleService $roleService,
        private RoleRepository $roleRepository
    ){}

    public function index(Request $request)
    {
        $this->roleService->setUser($request->user()->user);
        $this->roleRepository->setQuery(Role::query());
        $this->roleRepository->setPagination(true);
        $this->roleRepository->setOrderByColumn(
            $request->get('sort', 'label')
        );
        $this->roleRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->roleRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->roleRepository->setPage(
            $request->get('page', 1)
        );

        return RoleResource::collection(
            $this->roleRepository->findMany()
        );
    }

    public function store(StoreRoleRequest $request) {
        $this->roleService->setUser($request->user()->user);
        $create = $this->roleService->createrole($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating role',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Role created',
        ], Response::HTTP_OK);
    }

    public function update(Role $role, UpdateRoleRequest $request) {
        $this->roleService->setUser($request->user()->user);
        $create = $this->roleService->updateRole($role, $request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating role',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Role updated',
        ], Response::HTTP_OK);
    }
    
    
    public function destroy(Role $role) {
        $delete = $this->roleService->deleterole($role);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting role',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Role deleted',
        ], Response::HTTP_OK);
    }

}
