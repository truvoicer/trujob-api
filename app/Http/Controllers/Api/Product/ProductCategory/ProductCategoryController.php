<?php

namespace App\Http\Controllers\Api\Product\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Product\ProductCategory\UpdateProductCategoryRequest;
use App\Http\Resources\Product\Category\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Repositories\ProductCategoryRepository;
use App\Services\Product\ProductCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductCategoryController extends Controller
{

    public function __construct(
        private ProductCategoryService $productCategoryService,
        private ProductCategoryRepository $productCategoryRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->productCategoryRepository->setPagination(true);
        $this->productCategoryRepository->setOrderByColumn(
            $request->get('sort', 'label')
        );
        $this->productCategoryRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->productCategoryRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productCategoryRepository->setPage(
            $request->get('page', 1)
        );

        return ProductCategoryResource::collection(
            $this->productCategoryRepository->findMany()
        );
    }

    public function show(ProductCategory $productCategory) {
        return new ProductCategoryResource($productCategory);
    }

    public function store(StoreProductCategoryRequest $request) {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        if (
            $this->productCategoryService->createProductCategory(
                $request->validated(),
            )
        ) {
            return response()->json([
                'message' => 'Added product category',
            ], Response::HTTP_CREATED);
        }
        return response()->json([
            'message' => 'Error adding product category',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

public function update(ProductCategory $productCategory, UpdateProductCategoryRequest $request) {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        if (
            $this->productCategoryService->updateProductCategory(
                $productCategory,
                $request->validated(),
            )
        ) {
            return response()->json([
                'message' => 'Updated product category',
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => 'Error updating product category',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function destroy(ProductCategory $productCategory, Request $request) {
        $this->productCategoryService->setUser($request->user()->user);
        $this->productCategoryService->setSite($request->user()->site);

        if (
            $this->productCategoryService->deleteProductCategory(
                $productCategory,
            )
        ) {
            return response()->json([
                'message' => 'Removed product category',
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => 'Error removing product category',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
