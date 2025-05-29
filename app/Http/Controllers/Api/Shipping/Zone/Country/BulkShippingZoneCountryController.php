<?php

namespace App\Http\Controllers\Api\Shipping\Zone\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Zone\StoreBulkShippingZoneCountryRequest;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneService;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingZoneCountryController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
        private ShippingZoneRepository $shippingZoneRepository,
    )
    {
    }
    public function __invoke(ShippingZone $shippingZone, StoreBulkShippingZoneCountryRequest $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        if (!$this->shippingZoneService->syncCountries($shippingZone, $request->validated('ids'))) {
            return response()->json([
                'message' => 'Error syncing discounts to shipping zone',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Shipping zone discounts synced successfully',
        ], Response::HTTP_OK);
    }

}
