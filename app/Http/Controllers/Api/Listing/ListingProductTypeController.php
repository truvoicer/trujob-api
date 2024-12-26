<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingProductTypeRequest;
use App\Http\Requests\Listing\UpdateListingProductTypeRequest;
use App\Models\Listing;
use App\Models\ListingProductType;
use App\Models\ProductType;
use App\Services\Listing\ListingProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingProductTypeController extends Controller
{
    protected ListingProductTypeService $listingProductTypeService;

    public function __construct(ListingProductTypeService $listingProductTypeService, Request $request)
    {
        $this->listingProductTypeService = $listingProductTypeService;
    }

    public function addProductTypeToListing(Listing $listing, ProductType $productType, Request $request) {
        $this->listingProductTypeService->setUser($request->user());
        $this->listingProductTypeService->setListing($listing);
        $this->listingProductTypeService->setProductType($productType);
        $addProductType = $this->listingProductTypeService->addProductTypeToListing();
        if (!$addProductType) {
            return $this->sendErrorResponse(
                'Error adding listing product type',
                [],
                $this->listingProductTypeService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Added listing product type', [], $this->listingProductTypeService->getErrors());
    }

    public function removeProductTypeFromListing(Listing $listing, ListingProductType $listingProductType, Request $request) {
        $this->listingProductTypeService->setUser($request->user());
        $this->listingProductTypeService->setListing($listing);
        $this->listingProductTypeService->setListingProductType($listingProductType);
        $remove = $this->listingProductTypeService->removeProductTypeFromListing($request->all());
        if (!$remove) {
            return $this->sendErrorResponse(
                'Error removing listing product type',
                [],
                $this->listingProductTypeService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Removed listing product type', [], $this->listingProductTypeService->getErrors());
    }
}
