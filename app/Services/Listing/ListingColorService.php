<?php

namespace App\Services\Listing;

use App\Models\Color;
use App\Models\Listing;
use App\Services\BaseService;

class ListingColorService extends BaseService
{

    public function attachColorToListing(Listing $listing, Color $color) {
        $listing->colors()->attach($color->id);
        return true;
    }

    public function detachColorFromListing(Listing $listing, Color $color) {
        $listingColor = $listing->colors()->where('color_id', $color->id)->first();
        if (!$listingColor) {
            throw new \Exception('Listing color not found');
        }
        return $listingColor->delete();
    }
}
