<?php

namespace App\Http\Controllers\Api\Product\ProductType;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductProductTypeResource;
use App\Models\Product;
use App\Models\ProductType;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductProductTypeController extends Controller
{
    public function __construct(
        private ProductProductTypeService $productProductTypeService,
        private ProductRepository $productRepository,
    )
    {
    }

    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->productTypes()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->productRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->productRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productRepository->setPage(
            $request->get('page', 1)
        );

        return ProductProductTypeResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, ProductType $productType, Request $request)
    {
        $this->productProductTypeService->setUser($request->user()->user);
        $this->productProductTypeService->setSite($request->user()->site);

        if (
            !$this->productProductTypeService->attachProductTypeToProduct(
                $product,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error attaching product type to product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added product product type',
        ], Response::HTTP_CREATED);
    }
    
    public function destroy(Product $product, ProductType $productType, Request $request) {
        $this->productProductTypeService->setUser($request->user()->user);
        $this->productProductTypeService->setSite($request->user()->site);

        if (
            !$this->productProductTypeService->detachProductTypeFromProduct(
                $product,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error removing product product type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed product product type',
        ], Response::HTTP_OK);
    }

}
