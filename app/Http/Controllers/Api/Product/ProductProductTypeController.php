<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\Type\ProductTypeResource;
use App\Models\Product;
use App\Models\ProductType;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductProductTypeController extends Controller
{

    public function __construct(
        private ProductTypeService $productTypeService,
        private ProductRepository $productRepository,
     )
    {
    }

    public function index(Product $product, Request $request)
    {
        $this->productRepository->setQuery(
            $product->types()
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

        return ProductTypeResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, ProductType $productType, Request $request)
    {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);

        if (
            !$this->productTypeService->attachProductTypeToProduct(
                $product,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error attaching product type to product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product type attached to product',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, ProductType $productType, Request $request)
    {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);

        if (
            !$this->productTypeService->detachProductTypeFromProduct(
                $product,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error detaching product type from product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product type detached from product',
        ], Response::HTTP_OK);
    }

}
