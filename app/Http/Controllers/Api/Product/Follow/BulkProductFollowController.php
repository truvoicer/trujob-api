<?php

namespace App\Http\Controllers\Api\Product\Follow;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductFollowService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductFollowController extends Controller
{

    public function __construct(
        private ProductFollowService $productFollowService,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productFollowService->setUser($request->user()->user);
        $this->productFollowService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('users', 'id', 'user_ids')->validate();
        if (
            !$this->productFollowService->createProductFollow(
                $product,
                $request->get('user_ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product follows',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product follows created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productFollowService->setUser($request->user()->user);
        $this->productFollowService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('users', 'id', 'user_ids')->validate();
        if (
            !$this->productFollowService->detachBulkFollowsFromProduct(
                $product,
                $request->get('user_ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product follows',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product follows removed',
        ], Response::HTTP_OK);
    }
}
