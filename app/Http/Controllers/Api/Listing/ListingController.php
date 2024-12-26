<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\SaveListingRequest;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Resources\Listing\ListingCollection;
use App\Http\Resources\Listing\ListingSingleResource;
use App\Http\Resources\Listing\ListingListResource;
use App\Http\Resources\Listing\UserListingCollection;
use App\Models\Listing;
use App\Services\Listing\ListingsAdminService;
use App\Services\Listing\ListingsFetchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingController extends Controller
{
    protected ListingsAdminService $listingsAdminService;

    public function __construct(ListingsAdminService $listingsAdminService, Request $request)
    {
        $this->listingsAdminService = $listingsAdminService;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserListings(ListingsFetchService $listingsFetchService, Request $request)
    {
        $listingsFetchService->setUser($request->user());
        $listingsFetchService->setLimit($request->get('limit'));
//        $listingsFetchService->setOffset($request->get('offset'));
        $listingsFetchService->setPagination(true);
        return $this->sendSuccessResponse('Listing fetch',
            (new UserListingCollection($listingsFetchService->userListingsFetch())),
            []
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchPublicListings(ListingsFetchService $listingsFetchService, Request $request)
    {
        $listingsFetchService->setLimit($request->get('limit'));
//        $listingsFetchService->setOffset($request->get('offset'));
        $listingsFetchService->setPagination(true);
        return $this->sendSuccessResponse('Listing fetch',
            (new ListingCollection($listingsFetchService->listingsFetch())),
            []
        );
    }

    public function fetchSinglePublicListing(Listing $listing, ListingsFetchService $listingsFetchService)
    {
        return $this->sendSuccessResponse('Listing fetch',
            (new ListingListResource($listing)),
            []
        );
    }

    public function fetchSinglePrivateListing(Listing $listing, ListingsFetchService $listingsFetchService)
    {
        return $this->sendSuccessResponse('Listing fetch',
            (new ListingSingleResource($listing)),
            []
        );
    }

    public function initializeListing(Request $request) {
        $this->listingsAdminService->setUser($request->user());
        return $this->sendSuccessResponse('Listing created',
            [
                'code' => 'user_can_create_listing'
            ],
            $this->listingsAdminService->getErrors());
    }

    public function saveListing(Listing $listing, SaveListingRequest $request) {
        $this->listingsAdminService->setUser($request->user());
        $this->listingsAdminService->setListing($listing);
        $createListing = $this->listingsAdminService->saveListing($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error saving listing',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->sendSuccessResponse('Listing saved',
            ListingSingleResource::make($this->listingsAdminService->getListing()),
            $this->listingsAdminService->getErrors());
    }

    public function createListing(StoreListingRequest $request) {
        $this->listingsAdminService->setUser($request->user());
        $createListing = $this->listingsAdminService->createListing($request->validated());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error creating listing',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing created', [], $this->listingsAdminService->getErrors());
    }

    public function updateListing(Listing $listing, Request $request) {
        $this->listingsAdminService->setUser($request->user());
        $this->listingsAdminService->setListing($listing);
        $createListing = $this->listingsAdminService->updateListing($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error updating listing',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing updated', [], $this->listingsAdminService->getErrors());
    }
    public function deleteListing(Listing $listing) {
        $this->listingsAdminService->setListing($listing);
        $deleteListing = $this->listingsAdminService->deleteListing();
        if (!$deleteListing) {
            return $this->sendErrorResponse(
                'Error deleting listing',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing deleted', [], $this->listingsAdminService->getErrors());
    }
}
