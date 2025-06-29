<?php

namespace App\Http\Controllers\Api\Product\ProductCategory;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductCategoryController extends Controller
{

    public function __construct(
        private ProductCategoryService $productCategoryService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('categories')->validate();
        if (
            !$this->productCategoryService->attachBulkCategoriesToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product categories',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product categories created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('categories')->validate();
        if (
            !$this->productCategoryService->detachBulkCategoriesFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product categories',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product categories removed',
        ], Response::HTTP_CREATED);
    }
}
