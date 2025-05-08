<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreProductTypeRequest;
use App\Http\Requests\Listing\UpdateProductTypeRequest;
use App\Http\Resources\Listing\ColorCollection;
use App\Http\Resources\Listing\ListingProductTypeResource;
use App\Http\Resources\Listing\ProductTypeCollection;
use App\Models\ProductType;
use App\Repositories\ListingProductTypeRepository;
use App\Services\Listing\ListingProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeController extends Controller
{

    public function __construct(
        private ListingProductTypeService $productTypeService,
        private ListingProductTypeRepository $listingProductTypeRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->listingProductTypeRepository->setPagination(true);
        $this->listingProductTypeRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingProductTypeRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingProductTypeRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingProductTypeRepository->setPage(
            $request->get('page', 1)
        );
        
        return ListingProductTypeResource::collection(
            $this->listingProductTypeRepository->findMany()
        );
    }

    public function createProductType(Request $request) {
        $this->listingProductTypeService->setUser($request->user());
        $create = $this->listingProductTypeService->createProductType($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating product type',
                [],
                $this->listingProductTypeService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Product type created', [], $this->listingProductTypeService->getErrors());
    }

    public function updateProductType(ProductType $productType, Request $request) {
        $this->listingProductTypeService->setUser($request->user());
        $this->listingProductTypeService->setProductType($productType);
        $update = $this->listingProductTypeService->updateProductType($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating product type',
                [],
                $this->listingProductTypeService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Product type updated', [], $this->listingProductTypeService->getErrors());
    }
    public function deleteProductType(ProductType $productType) {
        $this->listingProductTypeService->setProductType($productType);
        $delete = $this->listingProductTypeService->deleteProductType();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting product type',
                [],
                $this->listingProductTypeService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Product type deleted', [], $this->listingProductTypeService->getErrors());
    }
}
