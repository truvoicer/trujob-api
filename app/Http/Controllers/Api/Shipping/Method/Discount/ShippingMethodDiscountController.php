<?php

namespace App\Http\Controllers\Api\Shipping\Method\Discount;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Discount;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Shipping\ShippingMethodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingMethodDiscountController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
        private ShippingMethodRepository $shippingMethodRepository,
    )
    {
    }
    public function index(ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodRepository->setQuery(
            $shippingMethod->discounts()
        );
        $this->shippingMethodRepository->setPagination(true);
        $this->shippingMethodRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->shippingMethodRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->shippingMethodRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingMethodRepository->setPage(
            $request->get('page', 1)
        );
        
        return DiscountResource::collection(
            $this->shippingMethodRepository->findMany()
        );
    }

    public function store(ShippingMethod $shippingMethod, Discount $discount, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        if ($shippingMethod->discounts()->where('discounts.id', $discount->id)->exists()) {
            return response()->json([
                'message' => 'Discount already exists in shipping method',
            ], Response::HTTP_BAD_REQUEST);
        }
        $shippingMethod->discounts()->attach(
            $discount->id
        );
        return response()->json([
            'message' => 'Shipping method discount created',
        ], Response::HTTP_OK);
    }

    public function destroy(ShippingMethod $shippingMethod, Discount $discount, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        
        $shippingMethod->discounts()->detach(
            $discount->id
        );

        return response()->json([
            'message' => 'Shipping method discount deleted',
        ], Response::HTTP_OK);
    }
}
