<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ProductType;
use App\Services\BaseService;

class ListingProductTypeService extends BaseService
{

    public function attachProductTypeToListing(Listing $listing, ProductType $productType) {
        $listing->productTypes()->attach($productType->id);
        return true;
    }

    public function detachProductTypeFromListing(Listing $listing, ProductType $productType) {
        $listingProductType = $listing->productTypes()->where('productType_id', $productType->id)->first();
        if (!$listingProductType) {
            throw new \Exception('Listing productType not found');
        }
        return $listingProductType->delete();
    }

}
