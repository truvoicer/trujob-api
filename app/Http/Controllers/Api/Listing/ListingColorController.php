<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingColorRequest;
use App\Http\Requests\Listing\UpdateListingColorRequest;
use App\Models\Color;
use App\Models\Listing;
use App\Models\ListingColor;
use App\Services\Listing\ListingColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingColorController extends Controller
{
    protected ListingColorService $listingColorService;

    public function __construct(ListingColorService $listingColorService, Request $request)
    {
        $this->listingColorService = $listingColorService;
    }

    public function addColorToListing(Listing $listing, Color $color, Request $request) {
        $this->listingColorService->setUser($request->user());
        $this->listingColorService->setListing($listing);
        $this->listingColorService->setColor($color);
        $addColor = $this->listingColorService->addColorToListing();
        if (!$addColor) {
            return $this->sendErrorResponse(
                'Error adding listing color',
                [],
                $this->listingColorService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Added listing color', [], $this->listingColorService->getErrors());
    }

    public function removeColorFromListing(Listing $listing, ListingColor $listingColor, Request $request) {
        $this->listingColorService->setUser($request->user());
        $this->listingColorService->setListing($listing);
        $this->listingColorService->setListingColor($listingColor);
        $remove = $this->listingColorService->removeColorFromListing($request->all());
        if (!$remove) {
            return $this->sendErrorResponse(
                'Error removing listing color',
                [],
                $this->listingColorService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Removed listing color', [], $this->listingColorService->getErrors());
    }
}
