<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingFeatureRequest;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingFeatureRequest;
use App\Http\Resources\Listing\ListingFeatureResource;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Repositories\ListingFeatureRepository;
use App\Services\Listing\ListingFeatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureController extends Controller
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
    public function index(Request $request) {
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
        
        return ListingFeatureResource::collection(
            $this->listingFeatureRepository->findMany()
        );
    }

    public function createListingFeature(Listing $listing, StoreListingRequest $request) {
        $this->listingFeatureService->setUser($request->user());
        $this->listingFeatureService->setListing($listing);
        $createListing = $this->listingFeatureService->createListingFeature($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error creating listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing created', [], $this->listingFeatureService->getErrors());
    }

    public function updateListingFeature(ListingFeature $listingFeature, Request $request) {
        $this->listingFeatureService->setUser($request->user());
        $this->listingFeatureService->setListingFeature($listingFeature);
        $createListing = $this->listingFeatureService->updateListingFeature($request->all());
        if (!$createListing) {
            return $this->sendErrorResponse(
                'Error updating listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing feature updated', [], $this->listingFeatureService->getErrors());
    }
    public function deleteListingFeature(ListingFeature $listingFeature) {
        $this->listingFeatureService->setListingFeature($listingFeature);
        $deleteListing = $this->listingFeatureService->deleteListingFeature();
        if (!$deleteListing) {
            return $this->sendErrorResponse(
                'Error deleting listing feature',
                [],
                $this->listingFeatureService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Listing feature deleted', [], $this->listingFeatureService->getErrors());
    }
}
