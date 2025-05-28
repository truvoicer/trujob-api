<?php

namespace App\Http\Controllers\Api\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Discount\Usage\UpdateUserDiscountUsageRequest;
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

    public function show(User $user, Discount $discount, Request $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);
        $userDiscountUsage = $this->userDiscountUsageService->getUserDiscountUsage($user, $discount);
        if (!$userDiscountUsage) {
            return response()->json([
                'message' => 'User discount usage not found for this discount',
            ], Response::HTTP_NOT_FOUND);
        }
        return new UserDiscountUsageResource(
            $userDiscountUsage
        );
    }

    public function store(User $user, Discount $discount, Request $request)
    {
        $this->userDiscountUsageService->setUser($request->user()->user);
        $this->userDiscountUsageService->setSite($request->user()->site);

        $this->userDiscountUsageService->createUsageTrack(
            $user,
            $discount
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
