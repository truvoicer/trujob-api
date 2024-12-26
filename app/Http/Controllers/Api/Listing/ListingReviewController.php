<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Services\Listing\ListingReviewService;
use App\Services\Listing\ListingsFetchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingReviewController extends Controller
{
    protected ListingReviewService $listingReviewService;

    public function __construct(ListingReviewService $listingReviewService, Request $request)
    {
        $this->listingReviewService = $listingReviewService;
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

    public function createListingReview(Listing $listing, StoreListingRequest $request) {
        $this->listingReviewService->setUser($request->user());
        $this->listingReviewService->setListing($listing);
        $createListingReview = $this->listingReviewService->createListingReview($request->all());
        if (!$createListingReview) {
            return $this->sendErrorResponse(
                'Error creating Listing review',
                [],
                $this->listingReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing review created', [], $this->listingReviewService->getErrors());
    }

    public function updateListingReview(ListingReview $listingReview, Request $request) {
        $this->listingReviewService->setUser($request->user());
        $this->listingReviewService->setListingReview($listingReview);
        $createListingReview = $this->listingReviewService->updateListingReview($request->all());
        if (!$createListingReview) {
            return $this->sendErrorResponse(
                'Error updating Listing review',
                [],
                $this->listingReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing follow updated', [], $this->listingReviewService->getErrors());
    }
    public function deleteListingReview(ListingReview $listingReview) {
        $this->listingReviewService->setListingReview($listingReview);
        $deleteListingReview = $this->listingReviewService->deleteListingReview();
        if (!$deleteListingReview) {
            return $this->sendErrorResponse(
                'Error deleting listing feature',
                [],
                $this->listingReviewService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing review deleted', [], $this->listingReviewService->getErrors());
    }
}
