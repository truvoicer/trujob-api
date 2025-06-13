<?php

namespace App\Http\Controllers\Api\Product\ProductType;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductProductTypeController extends Controller
{

    public function __construct(
        private ProductProductTypeService $productProductTypeService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productProductTypeService->setUser($request->user()->user);
        $this->productProductTypeService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('product_types')->validate();
        if (
            !$this->productProductTypeService->attachBulkProductTypesToProduct(
                $product,
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error creating product types',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product types created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productProductTypeService->setUser($request->user()->user);
        $this->productProductTypeService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('product_types')->validate();

        if (
            !$this->productProductTypeService->detachBulkProductTypesFromProduct(
                $product,
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing product types',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product types removed',
        ], Response::HTTP_OK);
    }
}
