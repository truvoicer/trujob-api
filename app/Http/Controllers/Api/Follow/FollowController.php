<?php

namespace App\Http\Controllers\Api\Follow;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingFollowRequest;
use App\Http\Requests\Listing\UpdateListingFollowRequest;
use App\Http\Resources\Listing\ListingFollowResource;
use App\Models\Listing;
use App\Models\ListingFollow;
use App\Models\User;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingFollowService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{

    public function __construct(
        private ListingFollowService $listingFollowService,
        private ListingRepository $listingRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Request $request)
    {
        $this->listingRepository->setQuery(
            $listing->follows()
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

        return ListingFollowResource::collection(
            $this->listingRepository->findMany()
        );
    }
    public function store(Listing $listing, StoreListingFollowRequest $request)
    {
        $this->listingFollowService->setUser($request->user()->user);
        $this->listingFollowService->setSite($request->user()->site);

        if (
            !$this->listingFollowService->createListingFollow(
                $listing,
                $request->validated('user_ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error creating listing follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing follow created',
        ], Response::HTTP_CREATED);
    }

    public function update(Listing $listing, ListingFollow $listingFollow, UpdateListingFollowRequest $request)
    {
        $this->listingFollowService->setUser($request->user()->user);
        $this->listingFollowService->setSite($request->user()->site);

        if (!$this->listingFollowService->updateListingFollow($listingFollow, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing follow updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Listing $listing, ListingFollow $listingFollow, Request $request)
    {
        $this->listingFollowService->setUser($request->user()->user);
        $this->listingFollowService->setSite($request->user()->site);

        if (!$this->listingFollowService->deleteListingFollow($listingFollow)) {
            return response()->json([
                'message' => 'Error deleting listing follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing follow deleted',
        ], Response::HTTP_OK);
    }
}
