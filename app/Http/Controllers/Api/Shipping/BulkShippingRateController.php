<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Shipping\ShippingRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingRateController extends Controller
{

    public function __construct(
        private ShippingRateService $shippingRateService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('shipping_rates')->validate();
        if (
            !$this->shippingRateService->destroyBulkShippingRates(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing shipping rates',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rates removed',
        ], Response::HTTP_OK);
    }
}
