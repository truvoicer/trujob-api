<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\FeatureResource;
use App\Models\Feature;
use App\Models\Listing;
use App\Repositories\ListingFeatureRepository;
use App\Services\Listing\ListingFeatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingFeatureController extends Controller
{

    public function __construct(
        private ListingFeatureService $listingFeatureService,
        private ListingFeatureRepository $listingFeatureRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Request $request) {
        $this->listingFeatureRepository->setQuery(
            $listing->features()
        );
        $this->listingFeatureRepository->setPagination(true);
        $this->listingFeatureRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingFeatureRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingFeatureRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingFeatureRepository->setPage(
            $request->get('page', 1)
        );
        
        return FeatureResource::collection(
            $this->listingFeatureRepository->findMany()
        );
    }

    public function create(Listing $listing, Feature $feature, Request $request) {
        $this->listingFeatureService->setUser($request->user()->user);
        $this->listingFeatureService->setSite($request->user()->site);

        if (
            !$this->listingFeatureService->attachFeatureToListing(
                $listing,
                $feature,
            )
        ) {
            return response()->json([
                'message' => 'Error creating listing feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing feature created',
        ], Response::HTTP_CREATED);
    }
    
    public function destroy(Listing $listing, Feature $feature, Request $request) {
        $this->listingFeatureService->setUser($request->user()->user);
        $this->listingFeatureService->setSite($request->user()->site);

        if (
            !$this->listingFeatureService->detachFeatureFromListing(
                $listing,
                $feature,
            )
        ) {
            return response()->json([
                'message' => 'Error removing listing feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing feature removed',
        ], Response::HTTP_CREATED);
    }
}
