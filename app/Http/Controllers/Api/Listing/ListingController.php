<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Requests\Listing\CreateListingRequest;
use App\Http\Requests\Listing\UpdateListingRequest;
use App\Http\Resources\Listing\ListingListResource;
use App\Models\Listing;
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

    public function show(Listing $listing)
    {
        return new ListingListResource($listing);
    }

    public function store(CreateListingRequest $request)
    {
        $this->listingsAdminService->setUser($request->user()->user);
        $this->listingsAdminService->setSite($request->user()->site);
        if (!$this->listingsAdminService->createListing($request->validated())) {
            return response()->json([
                'message' => 'Error creating listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing created',
        ], Response::HTTP_CREATED);
    }

    public function update(Listing $listing, UpdateListingRequest $request)
    {
        $this->listingsAdminService->setUser($request->user()->user);
        $this->listingsAdminService->setSite($request->user()->site);

        if (!$this->listingsAdminService->updateListing($listing, $request->all())) {
            return response()->json([
                'message' => 'Error updating listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Listing $listing, Request $request)
    {
        $this->listingsAdminService->setUser($request->user()->user);
        $this->listingsAdminService->setSite($request->user()->site);

        if (!$this->listingsAdminService->deleteListing($listing)) {
            return response()->json([
                'message' => 'Error deleting listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing deleted',
        ], Response::HTTP_OK);
    }
}
