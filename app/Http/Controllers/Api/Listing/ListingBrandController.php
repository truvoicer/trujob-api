<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingBrandRequest;
use App\Http\Requests\Listing\UpdateListingBrandRequest;
use App\Models\Brand;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Services\Listing\ListingBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingBrandController extends Controller
{
    protected ListingBrandService $listingBrandService;

    public function __construct(ListingBrandService $listingBrandService, Request $request)
    {
        $this->listingBrandService = $listingBrandService;
    }

    public function addBrandToListing(Listing $listing, Brand $brand, Request $request) {
        $this->listingBrandService->setUser($request->user());
        $this->listingBrandService->setListing($listing);
        $this->listingBrandService->setBrand($brand);
        $addBrand = $this->listingBrandService->addBrandToListing();
        if (!$addBrand) {
            return $this->sendErrorResponse(
                'Error adding listing brand',
                [],
                $this->listingBrandService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Added listing brand', [], $this->listingBrandService->getErrors());
    }

    public function removeBrandFromListing(Listing $listing, Brand $brand, Request $request) {
        $this->listingBrandService->setUser($request->user());
        $this->listingBrandService->setListing($listing);
        $this->listingBrandService->setBrand($brand);
        $removeBrandFromListing = $this->listingBrandService->removeBrandFromListing();
        if (!$removeBrandFromListing) {
            return $this->sendErrorResponse(
                'Error removing listing brand',
                [],
                $this->listingBrandService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Removed listing brand', [], $this->listingBrandService->getErrors());
    }
}
