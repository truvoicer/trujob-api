<?php

namespace App\Services\Listing;

use App\Models\Feature;
use App\Models\Listing;
use App\Services\BaseService;

class ListingFeatureService extends BaseService
{

    public function attachFeatureToListing(Listing $listing, Feature $feature) {
        $listing->features()->attach($feature->id);
        return true;
    }

    public function detachFeatureFromListing(Listing $listing, Feature $feature) {
        $listingFeature = $listing->features()->where('feature_id', $feature->id)->first();
        if (!$listingFeature) {
            throw new \Exception('Listing feature not found');
        }
        return $listingFeature->delete();
    }

}
