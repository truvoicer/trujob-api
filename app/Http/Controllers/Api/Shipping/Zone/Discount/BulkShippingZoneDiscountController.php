<?php

namespace App\Http\Controllers\Api\Shipping\Zone\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Zone\Discount\StoreBulkShippingZoneDiscountRequest;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneService;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingZoneDiscountController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
        private ShippingZoneRepository $shippingZoneRepository,
    )
    {
    }
    public function __invoke(ShippingZone $shippingZone, StoreBulkShippingZoneDiscountRequest $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        if (!$this->shippingZoneService->syncDiscounts($shippingZone, $request->validated('ids'))) {
            return response()->json([
                'message' => 'Error syncing discount to shipping zone',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Shipping zone discount synced successfully',
        ], Response::HTTP_OK);
    }

}
