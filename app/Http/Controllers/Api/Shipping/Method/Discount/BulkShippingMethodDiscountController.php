<?php

namespace App\Http\Controllers\Api\Shipping\Method\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Method\Discount\StoreBulkShippingMethodDiscountRequest;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Shipping\ShippingMethodService;
use Symfony\Component\HttpFoundation\Response;

class BulkShippingMethodDiscountController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
        private ShippingMethodRepository $shippingMethodRepository,
    )
    {
    }
    public function __invoke(ShippingMethod $shippingMethod, StoreBulkShippingMethodDiscountRequest $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        if (!$this->shippingMethodService->syncDiscounts($shippingMethod, $request->validated('ids'))) {
            return response()->json([
                'message' => 'Error syncing discount to shipping method',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Shipping method discount synced successfully',
        ], Response::HTTP_OK);
    }

}
