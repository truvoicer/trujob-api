<?php

namespace App\Http\Controllers\Api\Product\Color;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductColorController extends Controller
{

    public function __construct(
        private ProductColorService $productColorService,
    )
    {
    }

    public function store(Product $product, Request $request) {
        $this->productColorService->setUser($request->user()->user);
        $this->productColorService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('colors')->validate();
        if (
            !$this->productColorService->attachBulkColorsToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product colors',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product colors created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request) {
        $this->productColorService->setUser($request->user()->user);
        $this->productColorService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('colors')->validate();
        if (
            !$this->productColorService->detachBulkColorsFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product colors',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product colors removed',
        ], Response::HTTP_CREATED);
    }
}
