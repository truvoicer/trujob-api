<?php

namespace App\Http\Controllers\Api\Listing\ProductType;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingProductTypeResource;
use App\Models\Listing;
use App\Models\ProductType;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingProductTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingProductTypeController extends Controller
{
    public function __construct(
        private ListingProductTypeService $listingProductTypeService,
        private ListingRepository $listingRepository,
    )
    {
    }

    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->productTypes()
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

        return ListingProductTypeResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function create(Listing $listing, ProductType $productType, Request $request)
    {
        $this->listingProductTypeService->setUser($request->user()->user);
        $this->listingProductTypeService->setSite($request->user()->site);

        if (
            !$this->listingProductTypeService->attachProductTypeToListing(
                $listing,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error attaching product type to listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added listing product type',
        ], Response::HTTP_CREATED);
    }
    
    public function destroy(Listing $listing, ProductType $productType, Request $request) {
        $this->listingProductTypeService->setUser($request->user()->user);
        $this->listingProductTypeService->setSite($request->user()->site);

        if (
            !$this->listingProductTypeService->detachProductTypeFromListing(
                $listing,
                $productType,
            )
        ) {
            return response()->json([
                'message' => 'Error removing listing product type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed listing product type',
        ], Response::HTTP_OK);
    }

}
