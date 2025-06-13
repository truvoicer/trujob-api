<?php

namespace App\Http\Controllers\Api\Product\Review;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Review\BulkStoreProductReviewRequest;
use App\Models\Product;
use App\Services\Product\ProductReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductReviewController extends Controller
{

    public function __construct(
        private ProductReviewService $productReviewService,
    ) {}

    public function store(Product $product, BulkStoreProductReviewRequest $request)
    {
        $this->productReviewService->setUser($request->user()->user);
        $this->productReviewService->setSite($request->user()->site);

        if (
            !$this->productReviewService->attachBulkReviewsToProduct(
                $product,
                $request->validated('reviews', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product reviews',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product reviews created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productReviewService->setUser($request->user()->user);
        $this->productReviewService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('product_reviews')->validate();
        if (
            !$this->productReviewService->detachBulkReviewsFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product reviews',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product reviews removed',
        ], Response::HTTP_OK);
    }
}
