<?php

namespace App\Http\Controllers\Api\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\StoreDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Http\Resources\Discount\DiscountResource;
use App\Models\Discount;
use App\Repositories\DiscountRepository;
use App\Services\Discount\DiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountController extends Controller
{

    public function __construct(
        private DiscountService $discountService,
        private DiscountRepository $discountRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $this->discountRepository->setPagination(true);
        $this->discountRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->discountRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->discountRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->discountRepository->setPage(
            $request->get('page', 1)
        );
        
        return DiscountResource::collection(
            $this->discountRepository->findMany()
        );
    }

    public function create(StoreDiscountRequest $request) {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        if (!$this->discountService->createDiscount($request->validated())) {
            return response()->json([
                'message' => 'Error creating discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount created',
        ], Response::HTTP_CREATED);
    }

    public function update(Discount $discount, UpdateDiscountRequest $request) {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        if (!$this->discountService->updateDiscount($discount, $request->validated())) {
            return response()->json([
                'message' => 'Error updating discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Discount $discount, Request $request) {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);
        
        if (!$this->discountService->deleteDiscount($discount)) {
            return response()->json([
                'message' => 'Error deleting discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount deleted',
        ], Response::HTTP_OK);
    }
}
