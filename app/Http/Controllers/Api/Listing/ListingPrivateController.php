<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Resources\Listing\ListingSingleResource;
use App\Models\Listing;

class ListingPrivateController extends ListingBaseController
{

    public function show(Listing $listing)
    {
        return new ListingSingleResource($listing);
    }

}
