<?php

namespace App\Http\Controllers\Api\Product\Category;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\CategoryProductService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkCategoryProductController extends Controller
{

    public function __construct(
        private CategoryProductService $categoryProductService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->categoryProductService->setUser($request->user()->user);
        $this->categoryProductService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('categories')->validate();
        if (
            !$this->categoryProductService->attachBulkCategoriesToProduct(
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
        $this->categoryProductService->setUser($request->user()->user);
        $this->categoryProductService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('categories')->validate();
        if (
            !$this->categoryProductService->detachBulkCategoriesFromProduct(
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
