<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Resources\Listing\ListingListResource;
use App\Models\Listing;
use Illuminate\Http\Request;

class UserListingController extends ListingBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->listingsFetchService->setUser($request->user());
        $this->listingsFetchService->setLimit($request->get('limit'));
//        $listingsFetchService->setOffset($request->get('offset'));
        $this->listingsFetchService->setPagination(true);
        return ListingListResource::collection(
            $this->listingsFetchService->userListingsFetch()
        );
    }

    public function view(Listing $listing)
    {
        return new ListingListResource($listing);
    }
}
