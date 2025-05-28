<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingTypeResource;
use App\Models\Listing;
use App\Models\ListingType;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingListingTypeController extends Controller
{

    public function __construct(
        private ListingTypeService $listingTypeService,
        private ListingRepository $listingRepository,
     )
    {
    }

    public function index(Listing $listing, Request $request)
    {
        $this->listingRepository->setQuery(
            $listing->types()
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
        
        return ListingTypeResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function store(Listing $listing, ListingType $listingType, Request $request)
    {
        $this->listingTypeService->setUser($request->user()->user);
        $this->listingTypeService->setSite($request->user()->site);

        if (
            !$this->listingTypeService->attachListingTypeToListing(
                $listing,
                $listingType,
            )
        ) {
            return response()->json([
                'message' => 'Error attaching listing type to listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing type attached to listing',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Listing $listing, ListingType $listingType, Request $request)
    {
        $this->listingTypeService->setUser($request->user()->user);
        $this->listingTypeService->setSite($request->user()->site);

        if (
            !$this->listingTypeService->detachListingTypeFromListing(
                $listing,
                $listingType,
            )
        ) {
            return response()->json([
                'message' => 'Error detaching listing type from listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing type detached from listing',
        ], Response::HTTP_OK);
    }

}
