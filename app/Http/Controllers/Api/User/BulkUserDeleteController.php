<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\DeleteBatchUserRequest;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class BulkUserDeleteController extends Controller
{
    public function __invoke(
        DeleteBatchUserRequest $request
    ): \Illuminate\Http\JsonResponse
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        if (!$this->userAdminService->deleteBatchUser($request->validated('ids'))) {
            return response()->json([
                'message' => 'Error deleting users',
            ], 500);
        }
        return response()->json([
            'message' => 'Users deleted successfully',
        ], 200);
    }
}
