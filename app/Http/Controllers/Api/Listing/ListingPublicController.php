<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Resources\Listing\ListingListResource;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingPublicController extends ListingBaseController
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->listingsFetchService->setLimit($request->get('limit'));
//        $listingsFetchService->setOffset($request->get('offset'));
        $this->listingsFetchService->setPagination(true);
        return ListingListResource::collection(
            $this->listingsFetchService->listingsFetch()
        );
    }


}
