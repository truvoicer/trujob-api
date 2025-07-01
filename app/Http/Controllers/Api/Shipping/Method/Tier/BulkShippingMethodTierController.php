<?php

namespace App\Http\Controllers\Api\Shipping\Method\Tier;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Shipping\Tier\ShippingMethodTierService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingMethodTierController extends Controller
{

    public function __construct(
        private ShippingMethodTierService $shippingMethodTierService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->shippingMethodTierService->setUser($request->user()->user);
        $this->shippingMethodTierService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('shipping_method_tiers')->validate();
        if (
            !$this->shippingMethodTierService->destroyBulkShippingMethodTiers(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing shipping method tiers',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method tiers removed',
        ], Response::HTTP_OK);
    }
}
