<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingReviewRequest;
use App\Http\Requests\Listing\UpdateListingReviewRequest;
use App\Http\Resources\Listing\ListingReviewResource;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Repositories\ListingRepository;
use App\Repositories\ListingReviewRepository;
use App\Services\Listing\ListingReviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{

    public function __construct(
        private ListingReviewService $reviewService,
        private ListingReviewRepository $listingReviewRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $this->listingReviewRepository->setPagination(true);
        $this->listingReviewRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->listingReviewRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->listingReviewRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingReviewRepository->setPage(
            $request->get('page', 1)
        );
        
        return ListingReviewResource::collection(
            $this->listingReviewRepository->findMany()
        );
    }

    public function create(Listing $listing, StoreListingReviewRequest $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);

        if (!$this->reviewService->createlistingReview($listing, $request->validated())) {
            return response()->json([
                'message' => 'Error creating review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Review created',
        ], Response::HTTP_CREATED);
    }

    public function update(ListingReview $review, UpdateListingReviewRequest $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);

        if (!$this->reviewService->updatelistingReview($review, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ListingReview $review, Request $request) {
        $this->reviewService->setUser($request->user()->user);
        $this->reviewService->setSite($request->user()->site);
        
        if (!$this->reviewService->deletelistingReview($review)) {
            return response()->json([
                'message' => 'Error deleting listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review deleted',
        ], Response::HTTP_OK);
    }
}
