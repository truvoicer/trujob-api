<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Requests\Listing\ListingFetchRequest;
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
    public function index(ListingFetchRequest $request)
    {
        $this->listingsFetchService->setLimit($request->query->getInt('limit', 10));
        $this->listingsFetchService->setPage($request->query->getInt('page', 1));
        
        return ListingListResource::collection(
            $this->listingsFetchService->listingsFetch($request->validated())
        )->additional([
            'meta' => [
                'has_more' => $this->listingsFetchService->hasMore(),
            ]
        ]);
    }
}
