<?php

namespace App\Http\Controllers\Api\Product\Price;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductPriceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductPriceController extends Controller
{

    public function __construct(
        private ProductPriceService $productPriceService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('prices')->validate();
        if (
            !$this->productPriceService->attachBulkPricesToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product prices',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product prices created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('prices')->validate();

        if (
            !$this->productPriceService->detachBulkPricesFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product prices',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product prices removed',
        ], Response::HTTP_OK);
    }
}
