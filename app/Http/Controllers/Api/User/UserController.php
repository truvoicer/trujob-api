<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\UserAdminService;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        $this->userRepository->setQuery(
            $request->user()->site->users()
        );
        $this->userRepository->setPagination(true);
        $this->userRepository->setSortField(
            $request->get('sort', 'first_name')
        );
        $this->userRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->userRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->userRepository->setPage(
            $request->get('page', 1)
        );

        return UserResource::collection(
            $this->userRepository->findMany()
        );
    }


    /**
     * Gets a single user based on the id in the request url
     *
     */
    public function view(User $user, Request $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);
        return new UserResource($user);
    }

    /**
     * Creates a user based on the request post data
     *
     */
    public function create(CreateUserRequest $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        $requestData = $request->validated([
            'email',
            'password',
        ]);

        if (
            !$this->userAdminService->createUserByRoleId(
                $requestData,
                $request->validated('roles', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating user',
            ], 500);
        }
        return response()->json([
            'message' => 'User created',
        ], 201);
    }

    /**
     * Updates a user based on the post request data
     *
     */
    public function update(User $user, Request $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        if (
            !$this->userAdminService->updateUser(
                $user,
                $request->validated(),
                $request->validated('roles', [])
            )
        ) {
            return response()->json([
                'message' => 'Error updating user',
            ], 500);
        }
        return response()->json([
            'message' => 'User updated',
        ], 200);
    }

    /**
     * Deletes a user based on the post request data
     *
     */
    public function destroy(User $user, Request $request)
    {
        $this->userAdminService->setUser($request->user()->user);
        $this->userAdminService->setSite($request->user()->site);

        if (!$this->userAdminService->deleteUser($user)) {
            return response()->json([
                'message' => 'Error deleting user',
            ], 500);
        }
        return response()->json([
            'message' => 'User deleted',
        ], 200);
    }
}
