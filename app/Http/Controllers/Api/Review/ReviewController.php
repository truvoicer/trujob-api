<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Review\StoreProductReviewRequest;
use App\Http\Requests\Product\Review\UpdateProductReviewRequest;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use App\Repositories\ProductRepository;
use App\Repositories\ProductReviewRepository;
use App\Services\Product\ProductReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{

    public function __construct(
        private ProductReviewService $reviewService,
        private ProductReviewRepository $productReviewRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $this->productReviewRepository->setPagination(true);
        $this->productReviewRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->productReviewRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->productReviewRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productReviewRepository->setPage(
            $request->get('page', 1)
        );

        return ProductReviewResource::collection(
            $this->productReviewRepository->findMany()
        );
    }

    public function store(Product $product, StoreProductReviewRequest $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);

        if (!$this->reviewService->createproductReview($product, $request->validated())) {
            return response()->json([
                'message' => 'Error creating review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Review created',
        ], Response::HTTP_CREATED);
    }

    public function update(ProductReview $review, UpdateProductReviewRequest $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);

        if (!$this->reviewService->updateproductReview($review, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product review updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ProductReview $review, Request $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);

        if (!$this->reviewService->deleteproductReview($review)) {
            return response()->json([
                'message' => 'Error deleting product review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product review deleted',
        ], Response::HTTP_OK);
    }
}
