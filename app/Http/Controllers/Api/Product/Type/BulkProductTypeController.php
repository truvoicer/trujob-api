<?php

namespace App\Http\Controllers\Api\Product\Type;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductTypeController extends Controller
{

    public function __construct(
        private ProductTypeService $productTypeService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('product_types')->validate();
        if (
            !$this->productTypeService->attachBulkTypesToProduct(
                $product,
                $request->get('ids', []),
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
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('product_types')->validate();
        if (
            !$this->productTypeService->detachBulkTypesFromProduct(
                $product,
                $request->get('ids', []),
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
