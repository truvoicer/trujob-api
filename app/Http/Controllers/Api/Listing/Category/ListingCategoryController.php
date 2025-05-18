<?php

namespace App\Http\Controllers\Api\Listing\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingCategoryResource;
use App\Http\Resources\Listing\ListingListResource;
use App\Models\Category;
use App\Models\Listing;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingCategoryController extends Controller
{

    public function __construct(
        private ListingCategoryService $listingCategoryService,
        private ListingRepository $listingRepository,
    )
    {
    }

    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->categories()
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

        return ListingCategoryResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function create(Listing $listing, Category $category, Request $request) {
        $this->listingCategoryService->setUser($request->user()->user);
        $this->listingCategoryService->setSite($request->user()->site);

        if (
            $this->listingCategoryService->attachCategoryToListing(
                $listing,
                $category,
            )
        ) {
            return response()->json([
                'message' => 'Added listing category',
            ], Response::HTTP_CREATED);
        }
        return response()->json([
            'message' => 'Error adding listing category',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function destroy(Listing $listing, Category $category, Request $request) {
        $this->listingCategoryService->setUser($request->user()->user);
        $this->listingCategoryService->setSite($request->user()->site);

        if (
            $this->listingCategoryService->detachCategoryFromListing(
                $listing,
                $category,
            )
        ) {
            return response()->json([
                'message' => 'Removed listing category',
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => 'Error removing listing category',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
