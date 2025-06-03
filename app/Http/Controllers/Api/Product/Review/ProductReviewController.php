<?php

namespace App\Http\Controllers\Api\Product\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductReviewRequest;
use App\Http\Requests\Product\UpdateProductReviewRequest;
use App\Http\Resources\Product\ProductReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductReviewController extends Controller
{

    public function __construct(
        private ProductReviewService $productReviewService,
        private ProductRepository $productRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->productReview()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->productRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->productRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productRepository->setPage(
            $request->get('page', 1)
        );

        return ProductReviewResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, StoreProductReviewRequest $request) {
        $this->productReviewService->setUser($request->user()->user);
        $this->productReviewService->setSite($request->user()->site);

        if (!$this->productReviewService->createProductReview($product, $request->validated())) {
            return response()->json([
                'message' => 'Error creating product review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product review created',
        ], Response::HTTP_CREATED);
    }
    
    public function update(Product $product, ProductReview $productReview, UpdateProductReviewRequest $request) {
        $this->productReviewService->setUser($request->user()->user);
        $this->productReviewService->setSite($request->user()->site);

        if (!$this->productReviewService->updateProductReview($productReview, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product review updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Product $product, ProductReview $productReview, Request $request) {
        $this->productReviewService->setUser($request->user()->user);
        $this->productReviewService->setSite($request->user()->site);

        if (!$this->productReviewService->deleteProductReview($productReview)) {
            return response()->json([
                'message' => 'Error deleting product review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product review deleted',
        ], Response::HTTP_OK);
    }
}
