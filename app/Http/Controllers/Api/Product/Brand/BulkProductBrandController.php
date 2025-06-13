<?php

namespace App\Http\Controllers\Api\Product\Brand;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductBrandRepository;
use App\Services\Product\ProductBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductBrandController extends Controller
{

    public function __construct(
        private ProductBrandService $productBrandService,
        private ProductBrandRepository $productBrandRepository,
    ) {}

    public function store(Product $product, Request $request)
    {
        $this->productBrandService->setUser($request->user()->user);
        $this->productBrandService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('brands')->validate();
        if (
            !$this->productBrandService->attachBulkBrandsToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product brands',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product brands created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productBrandService->setUser($request->user()->user);
        $this->productBrandService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('brands')->validate();
        if (
            !$this->productBrandService->detachBulkBrandsFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product brands',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product brands removed',
        ], Response::HTTP_CREATED);
    }
}
