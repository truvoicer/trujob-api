<?php

namespace App\Http\Controllers\Api\Shipping\Zone;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Shipping\ShippingZoneService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingZoneController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('shipping_zones')->validate();
        if (
            !$this->shippingZoneService->destroyBulkShippingZones(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing shipping zones',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping zones removed',
        ], Response::HTTP_OK);
    }
}
