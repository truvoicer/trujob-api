<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\Permission\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PermissionController extends Controller
{

    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index(Request $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        $getPermissions = $this->permissionService->findByParams(
            $request->get('sort', "name"),
            $request->get('order', "asc"),
            $request->get('count', -1)
        );
        return PermissionResource::collection($getPermissions);
    }

    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }

    /**
     * Creates a new permission based on the request post data
     *
     * @param Request $request
     * @return PermissionResource|JsonResponse
     */
    public function store(StorePermissionRequest $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        $create = $this->permissionService->createPermission(
            $request->validated('name'),
            $request->validated('label')
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating permission.'
            ], Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            'message' => 'Permission created successfully.',
        ], Response::HTTP_CREATED);
    }

    /**
     * Updates a new permission based on request post data
     *
     * @param Permission $permission
     * @param Request $request
     * @return PermissionResource|JsonResponse
     */
    public function update(Permission $permission, UpdatePermissionRequest $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        $update = $this->permissionService->updatePermission(
            $permission, $request->validated()
        );
        if (!$update) {
            return response()->json([
                'message' => 'Error updating permission.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Permission updated successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * Deletes a permission based on the request post data
     *
     * @param Permission $permission
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Permission $permission, Request $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        if (!$this->permissionService->deletePermission($permission)) {
            return response()->json([
                'message' => 'Error deleting permission.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Permission deleted successfully.'
        ], Response::HTTP_OK);
    }
}
