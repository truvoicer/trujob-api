<?php

namespace App\Http\Controllers\Api\Product\Feature;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\FeatureResource;
use App\Models\Feature;
use App\Models\Product;
use App\Repositories\ProductFeatureRepository;
use App\Services\Product\ProductFeatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductFeatureController extends Controller
{

    public function __construct(
        private ProductFeatureService $productFeatureService,
        private ProductFeatureRepository $productFeatureRepository,
    )
    {
    }

    public function store(Product $product, Request $request) {
        $this->productFeatureService->setUser($request->user()->user);
        $this->productFeatureService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('features')->validate();

        if (
            !$this->productFeatureService->attachBulkFeaturesToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product features',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product features created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request) {
        $this->productFeatureService->setUser($request->user()->user);
        $this->productFeatureService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('features')->validate();

        if (
            !$this->productFeatureService->detachBulkFeaturesFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product features',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product features removed',
        ], Response::HTTP_CREATED);
    }
}
