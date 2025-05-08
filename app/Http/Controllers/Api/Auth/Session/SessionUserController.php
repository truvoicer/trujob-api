<?php

namespace App\Http\Controllers\Api\Auth\Session;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class SessionUserController extends Controller
{
    public function view(Request $request)
    {
        return new UserResource(
            $request->user()->user
        );
    }

    public function update(UpdateUserRequest $request)
    {
        $this->accessControlService->setUser($request->user()->user);
        
        if (!$this->userAdminService->updateUser(
            $request->user()->user,
            $request->validated(),
            $request->validated('roles', []),
        )) {
            return response()->json([
                'message' => 'Error updating user',
            ], 500);
        }
        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }
}
