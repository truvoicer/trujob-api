<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\BulkDestroyPermissionRequest;
use App\Http\Requests\Permission\BulkStorePermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
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
class BulkPermissionController extends Controller
{

    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Creates a new permission based on the request post data
     *
     * @param Request $request
     * @return PermissionResource|JsonResponse
     */
    public function store(BulkStorePermissionRequest $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        $create = $this->permissionService->bulkStorePermission(
            $request->validated('permissions')
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

    public function destroy(BulkDestroyPermissionRequest $request)
    {
        $this->permissionService->setUser($request->user()->user);
        $this->permissionService->setSite($request->user()->site);

        if (!$this->permissionService->bulkDeletePermission(
            $request->validated('ids')
        )) {
            return response()->json([
                'message' => 'Error deleting permission.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Permission deleted successfully.'
        ], Response::HTTP_OK);
    }
}
