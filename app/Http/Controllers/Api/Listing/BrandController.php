<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingBrandResource;
use App\Models\Brand;
use App\Repositories\ListingBrandRepository;
use App\Services\Listing\ListingBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{

    public function __construct(
        private ListingBrandService $listingBrandService,
        private ListingBrandRepository $listingBrandRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->listingBrandRepository->setPagination(true);
        $this->listingBrandRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingBrandRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingBrandRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingBrandRepository->setPage(
            $request->get('page', 1)
        );
        
        return ListingBrandResource::collection(
            $this->listingBrandRepository->findMany()
        );
    }

    public function createBrand(Request $request) {
        $this->listingBrandService->setUser($request->user());
        $create = $this->listingBrandService->createBrand($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating brand',
                [],
                $this->listingBrandService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Brand created', [], $this->listingBrandService->getErrors());
    }

    public function updateBrand(Brand $brand, Request $request) {
        $this->listingBrandService->setUser($request->user());
        $this->listingBrandService->setBrand($brand);
        $update = $this->listingBrandService->updateBrand($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating brand',
                [],
                $this->listingBrandService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Brand updated', [], $this->listingBrandService->getErrors());
    }
    public function deleteBrand(Brand $brand) {
        $this->listingBrandService->setBrand($brand);
        $delete = $this->listingBrandService->deleteBrand();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting brand',
                [],
                $this->listingBrandService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Brand deleted', [], $this->listingBrandService->getErrors());
    }

}
