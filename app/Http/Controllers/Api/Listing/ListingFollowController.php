<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingFollowRequest;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingFollowRequest;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingFollow;
use App\Services\Listing\ListingFollowService;
use App\Services\Listing\ListingsFetchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingFollowController extends Controller
{
    protected ListingFollowService $listingFollowService;

    public function __construct(ListingFollowService $listingFollowService, Request $request)
    {
        $this->listingFollowService = $listingFollowService;
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

    public function createListingFollow(Listing $listing, StoreListingRequest $request) {
        $this->listingFollowService->setUser($request->user());
        $this->listingFollowService->setListing($listing);
        $createListingFollow = $this->listingFollowService->createListingFollow($request->all());
        if (!$createListingFollow) {
            return $this->sendErrorResponse(
                'Error creating listing follow',
                [],
                $this->listingFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing follow created', [], $this->listingFollowService->getErrors());
    }

    public function updateListingFollow(ListingFollow $listingFollow, Request $request) {
        $this->listingFollowService->setUser($request->user());
        $this->listingFollowService->setListingFollow($listingFollow);
        $createListingFollow = $this->listingFollowService->updateListingFollow($request->all());
        if (!$createListingFollow) {
            return $this->sendErrorResponse(
                'Error updating listing follow',
                [],
                $this->listingFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing follow updated', [], $this->listingFollowService->getErrors());
    }
    public function deleteListingFollow(ListingFollow $listingFollow) {
        $this->listingFollowService->setListingFollow($listingFollow);
        $deleteListingFollow = $this->listingFollowService->deleteListingFollow();
        if (!$deleteListingFollow) {
            return $this->sendErrorResponse(
                'Error deleting listing feature',
                [],
                $this->listingFollowService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing follow deleted', [], $this->listingFollowService->getErrors());
    }
}
