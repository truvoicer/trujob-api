<?php

namespace App\Http\Controllers\Api\Listing\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingColorRequest;
use App\Http\Requests\Listing\UpdateListingColorRequest;
use App\Http\Resources\Listing\ListingColorResource;
use App\Models\Color;
use App\Models\Listing;
use App\Models\ListingColor;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingColorController extends Controller
{
    public function __construct(
        private ListingColorService $listingColorService,
        private ListingRepository $listingRepository,
    )
    {
    }

    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->colors()
        );
        $this->listingRepository->setPagination(true);
        $this->listingRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingRepository->setPage(
            $request->get('page', 1)
        );

        return ListingColorResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function store(Listing $listing, Color $color, Request $request)
    {
        $this->listingColorService->setUser($request->user()->user);
        $this->listingColorService->setSite($request->user()->site);

        if (
            !$this->listingColorService->attachColorToListing(
                $listing,
                $color,
            )
        ) {
            return response()->json([
                'message' => 'Error adding listing color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added listing color',
        ], Response::HTTP_CREATED);
    }
    public function destroy(Listing $listing, Color $color, Request $request)
    {
        $this->listingColorService->setUser($request->user()->user);
        $this->listingColorService->setSite($request->user()->site);

        if (
            !$this->listingColorService->detachColorFromListing(
                $listing,
                $color,
            )
        ) {
            return response()->json([
                'message' => 'Error removing listing color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed listing color',
        ], Response::HTTP_OK);
    }
}
