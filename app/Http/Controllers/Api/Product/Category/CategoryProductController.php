<?php

namespace App\Http\Controllers\Api\Product\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Product\CategoryProductService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryProductController extends Controller
{

    public function __construct(
        private CategoryProductService $categoryProductService,
        private ProductRepository $productRepository,
    )
    {
    }

    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->categories()
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

        return CategoryResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, Category $category, Request $request) {
        $this->categoryProductService->setUser($request->user()->user);
        $this->categoryProductService->setSite($request->user()->site);

        if (
            $this->categoryProductService->attachCategoryToProduct(
                $product,
                $category,
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

    public function destroy(Product $product, Category $category, Request $request) {
        $this->categoryProductService->setUser($request->user()->user);
        $this->categoryProductService->setSite($request->user()->site);

        if (
            $this->categoryProductService->detachCategoryFromProduct(
                $product,
                $category,
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
