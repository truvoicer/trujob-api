<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Requests\Listing\StoreListingMediaRequest;
use App\Http\Resources\Listing\ListingMediaResource;
use App\Http\Resources\Listing\ListingListResource;
use App\Models\Listing;
use App\Models\ListingMedia;
use App\Services\Listing\ListingsMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingMediaController extends ListingController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchMedia()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function createListingMedia(Listing $listing, Request $request)
    {
        $this->listingsAdminService->setUser($request->user());
        $this->listingsAdminService->setListing($listing);
        $createListingMedia = $this->listingsAdminService->createListingMedia($request->all());
        if (!$createListingMedia) {
            return $this->sendErrorResponse(
                'Error creating listing media',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Listing media created',
            new ListingListResource($this->listingsAdminService->getListing()),
            $this->listingsAdminService->getErrors()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreListingMediaRequest $request
     * @return JsonResponse
     */
    public function updateListingMedia(ListingMedia $listingMedia, Request $request, ListingsMediaService $listingsMediaService)
    {
        $listingsMediaService->setUser($request->user());
        $listingsMediaService->setListingMedia($listingMedia);
        $createListingMedia = $listingsMediaService->updateListingMedia($request->all());
        if (!$createListingMedia) {
            return $this->sendErrorResponse(
                'Error updating listing media',
                [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Listing media updated',
            new ListingMediaResource($listingsMediaService->getListingMedia()),
            $this->listingsAdminService->getErrors()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreListingMediaRequest $request
     * @return JsonResponse
     */
    public function deleteListingMedia(ListingMedia $listingMedia, ListingsMediaService $listingsMediaService)
    {
        $listingsMediaService->setListingMedia($listingMedia);
        $deleteListingMedia = $listingsMediaService->deleteListingMedia();
        if (!$deleteListingMedia) {
            return $this->sendErrorResponse(
                'Error deleting listing media',
            [],
                $this->listingsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Listing media deleted',
            [],
            $this->listingsAdminService->getErrors()
        );
    }


}
