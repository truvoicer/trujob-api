<?php

namespace App\Http\Controllers\Api\Shipping\Method;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Shipping\ShippingMethodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingMethodController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('shipping_methods')->validate();
        if (
            !$this->shippingMethodService->destroyBulkShippingMethods(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing shipping methods',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping methods removed',
        ], Response::HTTP_OK);
    }
}
