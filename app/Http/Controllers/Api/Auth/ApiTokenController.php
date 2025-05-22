<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\PersonalAccessTokenResource;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\User\UserAdminService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class ApiTokenController extends Controller
{
    public function __construct(
        protected UserAdminService $userAdminService,
        protected UserRepository $userRepository,
    ) {
    }
    
    public function index(Request $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        $this->userRepository->setQuery(
            $request->user()->user->tokens()
        );
        $this->userRepository->setPagination(true);
        $this->userRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->userRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->userRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->userRepository->setPage(
            $request->get('page', 1)
        );

        return PersonalAccessTokenResource::collection(
            $this->userRepository->findMany()
        );
    }

    public function view(PersonalAccessToken $personalAccessToken, Request $request)
    {
        return new PersonalAccessTokenResource(
            $personalAccessToken
        );
    }

    public function create(Request $request)
    {
        return new PersonalAccessTokenResource(
            $this->userAdminService->createUserToken(
                $request->user(),
            )
        );
    }
    public function update(PersonalAccessToken $personalAccessToken, UpdateUserRequest $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        $update = $this->userAdminService->updateApiTokenExpiry(
            $personalAccessToken,
            $request->validated()
        );
        if (!$update) {
            return response()->json([
                'message' => 'Error updating api token',
            ], 500);
        }
        return response()->json([
            'message' => 'Api Token updated successfully',
        ], 200);
    }

    public function destroy(PersonalAccessToken $personalAccessToken, Request $request)
    {
        $delete = $this->userAdminService->deleteApiToken($personalAccessToken);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting api token',
            ], 500);
        }
        return response()->json([
            'message' => 'Api Token deleted successfully',
        ], 200);
    }

}
