<?php

namespace App\Http\Controllers\Api\Price\Discount;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Price\PriceDiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkPriceDiscountController extends Controller
{

    public function __construct(
        private PriceDiscountService $priceDiscountService,
    )
    {
    }

    public function store(Price $price, Request $request) {
        $this->priceDiscountService->setUser($request->user()->user);
        $this->priceDiscountService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('discounts')->validate();
        if (
            !$this->priceDiscountService->attachBulkDiscountsToPrice(
                $price,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product discounts',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price discounts created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Price $price, Request $request) {
        $this->priceDiscountService->setUser($request->user()->user);
        $this->priceDiscountService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('discounts')->validate();

        if (
            !$this->priceDiscountService->detachBulkDiscountsFromPrice(
                $price,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing price discounts',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price discounts removed',
        ], Response::HTTP_OK);
    }
}
