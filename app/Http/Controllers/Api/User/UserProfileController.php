<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserProfileRequest;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController extends Controller
{

    public function update(UpdateUserProfileRequest $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        if (!$this->userAdminService->updateUser(
            $request->user()->user,
            $request->validated()
        )) {
            return response()->json([
                'message' => 'Error updating user profile',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'User profile updated successfully',
        ], Response::HTTP_OK);
    }
}
