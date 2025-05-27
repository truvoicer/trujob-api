<?php

namespace App\Http\Controllers\Api\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\Usage\StoreUserDiscountUsageRequest;
use App\Http\Requests\Discount\Usage\UpdateUserDiscountUsageRequest;
use App\Http\Resources\Discount\UserDiscountUsageResource;
use App\Models\Discount;
use App\Models\User;
use App\Repositories\UserDiscountUsageRepository;
use App\Services\Discount\UserDiscountUsageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserDiscountUsageController extends Controller
{
    // This controller is responsible for handling userDiscountUsage-related operations.
    // It will contain methods to create, update, delete, and retrieve userDiscountUsages.
    // The methods will interact with the UserDiscountUsageService to perform the necessary operations.

    public function __construct(
        private UserDiscountUsageService $userDiscountUsageService,
        private UserDiscountUsageRepository $userDiscountUsageRepository

    ) {}

    public function index(User $user, Discount $discount, Request $request)
    {

        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        $this->userDiscountUsageRepository->setPagination(true);
        $this->userDiscountUsageRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->userDiscountUsageRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->userDiscountUsageRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->userDiscountUsageRepository->setPage(
            $request->get('page', 1)
        );

        return UserDiscountUsageResource::collection(
            $this->userDiscountUsageRepository->findMany()
        );
    }

    public function view(User $user, Discount $discount, Request $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        return new UserDiscountUsageResource(
            $this->userDiscountUsageService->getUserDiscountUsage($user, $discount)
        );
    }

    public function create(User $user, Discount $discount, StoreUserDiscountUsageRequest $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        $check = $user->discountUsages()->where('discount.id', $discount->id)->exists();
        if ($check) {
            return response()->json([
                'message' => 'User discount usage already exists for this discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->userDiscountUsageService->createUsageTrack(
            $user,
            $discount, 
            $request->validated()
        );

        return response()->json([
            'message' => 'UserDiscountUsage created',
        ], Response::HTTP_OK);
    }

    public function update(User $user, Discount $discount, UpdateUserDiscountUsageRequest $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        
        $usage = $user->discountUsages()->where('discount_id', $discount->id)->first();
        if (!$usage) {
            throw new \Exception('User discount usage does not exist for this discount');
        }
        $update = $this->userDiscountUsageService->updateUserDiscountUsage(
            $usage, 
            $request->validated()
        );
        if (!$update->exists()) {
            return response()->json([
                'message' => 'Error updating user discount usage',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'UserDiscountUsage updated',
        ], Response::HTTP_OK);
    }

    public function destroy(User $user, Discount $discount, Request $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        $usage = $user->discountUsages()->where('discount_id', $discount->id)->first();
        if (!$usage) {
            throw new \Exception('User discount usage does not exist for this discount');
        }
        $delete = $this->userDiscountUsageService->deleteUserDiscountUsage($usage);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting userDiscountUsage',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'UserDiscountUsage deleted',
        ], Response::HTTP_OK);
    }
}
