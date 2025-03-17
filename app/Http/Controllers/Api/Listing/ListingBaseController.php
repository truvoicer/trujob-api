<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Services\Listing\ListingsAdminService;
use App\Services\Listing\ListingsFetchService;

class ListingBaseController extends Controller
{
    protected ListingsAdminService $listingsAdminService;
    protected ListingsFetchService $listingsFetchService;

    public function __construct()
    {
        $this->listingsAdminService = app(ListingsAdminService::class);
        $this->listingsFetchService = app(ListingsFetchService::class);
    }
}
