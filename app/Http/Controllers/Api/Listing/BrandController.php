<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreBrandRequest;
use App\Http\Requests\Listing\UpdateBrandRequest;
use App\Http\Resources\Listing\BrandCollection;
use App\Http\Resources\Listing\CategoryCollection;
use App\Models\Brand;
use App\Services\Listing\ListingBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    protected ListingBrandService $listingBrandService;

    public function __construct(ListingBrandService $brandService, Request $request)
    {
        $this->listingBrandService = $brandService;
    }

    public function fetchBrands(Request $request) {
        $this->listingBrandService->setPagination(true);
        return $this->sendSuccessResponse(
            'Brand fetch',
            ( new BrandCollection($this->listingBrandService->brandFetch())),
            $this->listingBrandService->getErrors());
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
