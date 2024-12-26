<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingFeatureRequest;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingFeatureRequest;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Services\Listing\ListingFeatureService;
use App\Services\Listing\ListingsAdminService;
use App\Services\Listing\ListingsFetchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingFeatureController extends Controller
{
    protected ListingFeatureService $listingFeatureService;

    public function __construct(ListingFeatureService $listingFeatureService, Request $request)
    {
        $this->listingFeatureService = $listingFeatureService;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchListingsFeature(ListingsFetchService $listingsFetchService)
    {
        //
    }

    public function createListingFeature(Listing $listing, StoreListingRequest $request) {
        $this->listingFeatureService->setUser($request->user());
        $this->listingFeatureService->setListing($listing);
        $createListing = $this->listingFeatureService->createListingFeature($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error creating listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing created', [], $this->listingFeatureService->getErrors());
    }

    public function updateListingFeature(ListingFeature $listingFeature, Request $request) {
        $this->listingFeatureService->setUser($request->user());
        $this->listingFeatureService->setListingFeature($listingFeature);
        $createListing = $this->listingFeatureService->updateListingFeature($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error updating listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing feature updated', [], $this->listingFeatureService->getErrors());
    }
    public function deleteListingFeature(ListingFeature $listingFeature) {
        $this->listingFeatureService->setListingFeature($listingFeature);
        $deleteListing = $this->listingFeatureService->deleteListingFeature();
        if (!$deleteListing) {
            return $this->sendErrorResponse(
                'Error deleting listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing feature deleted', [], $this->listingFeatureService->getErrors());
    }
}
