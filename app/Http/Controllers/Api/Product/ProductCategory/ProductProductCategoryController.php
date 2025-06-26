<?php

namespace App\Http\Controllers\Api\Product\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\Category\ProductCategoryResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductProductCategoryController extends Controller
{
    public function __construct(
        private ProductCategoryService $productCategoryService,
        private ProductRepository $productRepository,
    )
    {
    }

    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->productCategories()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setOrderByColumn(
            $request->get('sort', 'label')
        );
        $this->productRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->productRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productRepository->setPage(
            $request->get('page', 1)
        );

        return ProductCategoryResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, ProductCategory $productCategory, Request $request)
    {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        if (
            !$this->productCategoryService->attachCategoryToProduct(
                $product,
                $productCategory,
            )
        ) {
            return response()->json([
                'message' => 'Error attaching product category to product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added product product category',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, ProductCategory $productCategory, Request $request) {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        if (
            !$this->productCategoryService->detachCategoryFromProduct(
                $product,
                $productCategory,
            )
        ) {
            return response()->json([
                'message' => 'Error removing product category from product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed product product category',
        ], Response::HTTP_OK);
    }

}
