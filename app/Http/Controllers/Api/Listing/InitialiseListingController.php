<?php

namespace App\Http\Controllers\Api\Listing;

use Illuminate\Http\Request;

class InitialiseListingController extends ListingBaseController
{

    public function __invoke(Request $request)
    {
        $this->listingsAdminService->setUser($request->user());
        return $this->sendSuccessResponse(
            'Listing created',
            [
                'code' => 'user_can_create_listing'
            ],
            $this->listingsAdminService->getErrors()
        );
    }
}
