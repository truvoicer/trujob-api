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

class ListingController extends ListingBaseController
{

    public function index(Request $request)
    {
        $this->listingsFetchService->setUser($request->user()->user);
        $this->listingsFetchService->setLimit($request->get('limit', 10));
        $this->listingsFetchService->setOffset($request->get('offset', 0));
        $this->listingsFetchService->setPage($request->get('page', 1));
        $this->listingsFetchService->setPagination(true);
        return ListingListResource::collection(
            $this->listingsFetchService->listingsFetch()
        );
    }

    public function view(Listing $listing)
    {
        return new ListingListResource($listing);
    }

    public function create(StoreListingRequest $request)
    {
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

    public function update(Listing $listing, Request $request)
    {
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

    public function destroy(Listing $listing)
    {
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
