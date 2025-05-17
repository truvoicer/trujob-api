<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingReviewRequest;
use App\Http\Requests\Listing\UpdateListingReviewRequest;
use App\Http\Resources\Listing\ListingReviewResource;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingReviewController extends Controller
{

    public function __construct(
        private ListingReviewService $listingReviewService,
        private ListingRepository $listingRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->listingReview()
        );
        $this->listingRepository->setPagination(true);
        $this->listingRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->listingRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->listingRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingRepository->setPage(
            $request->get('page', 1)
        );

        return ListingReviewResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function create(Listing $listing, StoreListingReviewRequest $request) {
        $this->listingReviewService->setUser($request->user()->user);
        $this->listingReviewService->setSite($request->user()->site);

        if (!$this->listingReviewService->createListingReview($listing, $request->validated())) {
            return response()->json([
                'message' => 'Error creating listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review created',
        ], Response::HTTP_CREATED);
    }
    
    public function update(Listing $listing, ListingReview $listingReview, UpdateListingReviewRequest $request) {
        $this->listingReviewService->setUser($request->user()->user);
        $this->listingReviewService->setSite($request->user()->site);

        if (!$this->listingReviewService->updateListingReview($listingReview, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Listing $listing, ListingReview $listingReview, Request $request) {
        $this->listingReviewService->setUser($request->user()->user);
        $this->listingReviewService->setSite($request->user()->site);

        if (!$this->listingReviewService->deleteListingReview($listingReview)) {
            return response()->json([
                'message' => 'Error deleting listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review deleted',
        ], Response::HTTP_OK);
    }
}
