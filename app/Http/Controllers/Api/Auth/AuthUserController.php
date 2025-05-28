<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthUserController extends Controller
{

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $token = $this->userAdminService->getlatestToken($request->user());
        return response()->json([
            'message' => 'User logged in',
            'data' => [
                'user' => new UserResource($request->user()->user),
                'token' => new AccessTokenResource($token),
            ]
        ], Response::HTTP_OK);
    }
}
