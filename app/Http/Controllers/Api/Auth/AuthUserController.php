<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{

    public function view(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
