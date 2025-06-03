<?php

namespace App\Http\Controllers\Api\Product\Brand;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductBrandResource;
use App\Models\Brand;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductBrandController extends Controller
{

    public function __construct(
        private ProductBrandService $productBrandService,
        private ProductRepository $productRepository,
    ) {}

    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->brands()
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

        return ProductBrandResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, Brand $brand, Request $request)
    {
        $this->productBrandService->setUser($request->user()->user);
        $this->productBrandService->setSite($request->user()->site);

        if (
            !$this->productBrandService->attachBrandToProduct(
                $product,
                $brand,
            )
        ) {
            return response()->json([
                'message' => 'Error adding product brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added product brand',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Brand $brand, Request $request)
    {
        $this->productBrandService->setUser($request->user()->user);
        $this->productBrandService->setSite($request->user()->site);

        if (
            !$this->productBrandService->detachBrandFromProduct(
                $product,
                $brand,
            )
        ) {
            return response()->json([
                'message' => 'Error removing product brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed product brand',
        ], Response::HTTP_OK);
    }
}
