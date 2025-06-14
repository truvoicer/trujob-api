<?php

namespace App\Http\Controllers\Api\Shipping\Method\Restriction;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Shipping\ShippingRestrictionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingMethodRestrictionController extends Controller
{

    public function __construct(
        private ShippingRestrictionService $shippingRestrictionService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->shippingRestrictionService->setUser($request->user()->user);
        $this->shippingRestrictionService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('shipping_restrictions')->validate();
        if (
            !$this->shippingRestrictionService->destroyBulkShippingRestrictions(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing shipping restrictions',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping restrictions removed',
        ], Response::HTTP_OK);
    }
}
