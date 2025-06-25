<?php

namespace App\Http\Controllers\Api\Product\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\Feature\FeatureResource;
use App\Models\Feature;
use App\Models\Product;
use App\Repositories\ProductFeatureRepository;
use App\Services\Product\ProductFeatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductFeatureController extends Controller
{

    public function __construct(
        private ProductFeatureService $productFeatureService,
        private ProductFeatureRepository $productFeatureRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product, Request $request) {
        $this->productFeatureRepository->setQuery(
            $product->features()
        );
        $this->productFeatureRepository->setPagination(true);
        $this->productFeatureRepository->setOrderByColumn(
            $request->get('sort', 'label')
        );
        $this->productFeatureRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->productFeatureRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productFeatureRepository->setPage(
            $request->get('page', 1)
        );

        return FeatureResource::collection(
            $this->productFeatureRepository->findMany()
        );
    }

    public function store(Product $product, Feature $feature, Request $request) {
        $this->productFeatureService->setUser($request->user()->user);
        $this->productFeatureService->setSite($request->user()->site);

        if (
            !$this->productFeatureService->attachFeatureToProduct(
                $product,
                $feature,
            )
        ) {
            return response()->json([
                'message' => 'Error creating product feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product feature created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Feature $feature, Request $request) {
        $this->productFeatureService->setUser($request->user()->user);
        $this->productFeatureService->setSite($request->user()->site);

        if (
            !$this->productFeatureService->detachFeatureFromProduct(
                $product,
                $feature,
            )
        ) {
            return response()->json([
                'message' => 'Error removing product feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product feature removed',
        ], Response::HTTP_CREATED);
    }
}
