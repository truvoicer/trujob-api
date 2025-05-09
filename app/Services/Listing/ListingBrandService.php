<?php

namespace App\Services\Listing;

use App\Models\Brand;
use App\Models\Listing;
use App\Services\BaseService;
use App\Services\FetchService;

class ListingBrandService extends BaseService
{

    public function attachBrandToListing(Listing $listing, Brand $brand) {
        $listing->brands()->attach($brand->id);
        return true;
    }

    public function detachBrandFromListing(Listing $listing, Brand $brand) {
        $listingBrand = $listing->brands()->where('brand_id', $brand->id)->first();
        if (!$listingBrand) {
            throw new \Exception('Listing brand not found');
        }
        return $listingBrand->delete();
    }

}
