<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingBrandResource;
use App\Models\Brand;
use App\Models\Listing;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingBrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingBrandController extends Controller
{

    public function __construct(
        private ListingBrandService $listingBrandService,
        private ListingRepository $listingRepository,
    ) {}

    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->brands()
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

        return ListingBrandResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function create(Listing $listing, Brand $brand, Request $request)
    {
        $this->listingBrandService->setUser($request->user()->user);
        $this->listingBrandService->setSite($request->user()->site);

        if (
            !$this->listingBrandService->attachBrandToListing(
                $listing,
                $brand,
            )
        ) {
            return response()->json([
                'message' => 'Error adding listing brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added listing brand',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Listing $listing, Brand $brand, Request $request)
    {
        $this->listingBrandService->setUser($request->user()->user);
        $this->listingBrandService->setSite($request->user()->site);

        if (
            !$this->listingBrandService->detachBrandFromListing(
                $listing,
                $brand,
            )
        ) {
            return response()->json([
                'message' => 'Error removing listing brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed listing brand',
        ], Response::HTTP_OK);
    }
}
