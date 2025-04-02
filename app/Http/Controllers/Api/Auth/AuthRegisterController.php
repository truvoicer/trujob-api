<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Auth\ApiAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRegisterUserRequest;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Services\User\RoleService;
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
        $site = $request->user();

        if (!$site instanceof Site) {
            return response()->json([
                'message' => 'Invalid site'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role = $this->roleService->getRoleRepository()->findOneBy(
            [['name', '=', ApiAbility::USER->value]]
        );

        if (!$role instanceof Role) {
            throw new \Exception("Error finding role");
        }
        
        $user = User::where('email', $request->get('email'))->first();
        if ($user) {
            $token = $this->userAdminService->createSiteUserToken(
                $this->userAdminService->registerSiteUser($site, $user)
            );
        } else {
            if (!$this->userAdminService->createUser($request->validated(), [$role->id])) {
                throw new \Exception("Error creating user");
            }
            $user = $this->userAdminService->getUserRepository()->getModel();
            $token = $this->userAdminService->createSiteUserToken(
                $this->userAdminService->registerSiteUser($site, $user)
            );
        }
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
