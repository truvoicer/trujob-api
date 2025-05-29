<?php

namespace App\Http\Controllers\Api\Shipping\Zone\Discount;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Shipping\ShippingZoneResource;
use App\Models\Discount;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingZoneDiscountController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
        private ShippingZoneRepository $shippingZoneRepository,
    )
    {
    }
    public function index(ShippingZone $shippingZone, Request $request) {
        $this->shippingZoneRepository->setQuery(
            $shippingZone->discounts()
        );
        $this->shippingZoneRepository->setPagination(true);
        $this->shippingZoneRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->shippingZoneRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->shippingZoneRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingZoneRepository->setPage(
            $request->get('page', 1)
        );
        
        return DiscountResource::collection(
            $this->shippingZoneRepository->findMany()
        );
    }

    public function store(ShippingZone $shippingZone, Discount $discount, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        if ($shippingZone->discounts()->where('discounts.id', $discount->id)->exists()) {
            return response()->json([
                'message' => 'Discount already exists in shipping zone',
            ], Response::HTTP_BAD_REQUEST);
        }
        $shippingZone->discounts()->attach(
            $discount->id
        );
        return response()->json([
            'message' => 'Shipping zone discount created',
        ], Response::HTTP_OK);
    }

    public function destroy(ShippingZone $shippingZone, Discount $discount, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        
        $shippingZone->discounts()->detach(
            $discount->id
        );

        return response()->json([
            'message' => 'Shipping zone discount deleted',
        ], Response::HTTP_OK);
    }
}
