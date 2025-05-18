<?php

namespace App\Http\Controllers\Api\ProductType;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductType\StoreProductTypeRequest;
use App\Http\Requests\ProductType\UpdateProductTypeRequest;
use App\Http\Resources\Listing\ProductTypeResource;
use App\Models\ProductType;
use App\Repositories\ProductTypeRepository;
use App\Services\ProductType\ProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeController extends Controller
{

    public function __construct(
        private ProductTypeService $productTypeService,
        private ProductTypeRepository $productTypeRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->productTypeRepository->setPagination(true);
        $this->productTypeRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->productTypeRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->productTypeRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productTypeRepository->setPage(
            $request->get('page', 1)
        );
        
        return ProductTypeResource::collection(
            $this->productTypeRepository->findMany()
        );
    }

    public function create(StoreProductTypeRequest $request) {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);
        
        if (!$this->productTypeService->createProductType($request->validated())) {
            return response()->json([
                'message' => 'Error creating product type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product type created',
        ], Response::HTTP_OK);
    }

    public function update(ProductType $productType, UpdateProductTypeRequest $request) {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);

        if (!$this->productTypeService->updateProductType($productType, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product type updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ProductType $productType, Request $request) {
        $this->productTypeService->setUser($request->user()->user);
        $this->productTypeService->setSite($request->user()->site);
        
        if (!$this->productTypeService->deleteProductType($productType)) {
            return response()->json([
                'message' => 'Error deleting product type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product type deleted',
        ], Response::HTTP_OK);
    }
}
