<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthLoginController extends Controller
{

    public function __invoke(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid user'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->userAdminService->createUserToken($user);

        if (!$token) {
            return response()->json([
                'message' => 'Error creating token'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'User logged in',
            'data' => [
                'user' => new UserResource($user),
                'token' => new AccessTokenResource($token),
            ]
        ], Response::HTTP_OK);
    }
}
