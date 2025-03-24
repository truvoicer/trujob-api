<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Auth\ApiAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRegisterUserRequest;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\User\RoleService;
use App\Services\User\UserAdminService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthRegisterController extends Controller
{
    public function __construct(
        private RoleService $roleService
    )
    {
        parent::__construct();   
    }
    public function __invoke(AuthRegisterUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $role = $this->roleService->getRoleRepository()->findOneBy(
            [['name', '=', ApiAbility::USER->value]]
        );

        if (!$role instanceof Role) {
            throw new \Exception("Error finding role");
        }
        if (!$this->userAdminService->createUser($request->validated(), [$role->id])) {
            throw new \Exception("Error creating user");
        }
        $user = $this->userAdminService->getUserRepository()->getModel();
        $token = $this->userAdminService->createUserTokenByRoleId($user, $role->id);
        if (!$token) {
            throw new \Exception("Error creating token");
        }
        return response()->json([
            'message' => 'User created',
            'data' => [
                'user' => new UserResource($user),
                'token' => new AccessTokenResource($token),
            ]
        ], Response::HTTP_CREATED);
    }
}
