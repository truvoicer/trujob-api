<?php

namespace App\Services\Listing;

use App\Models\Feature;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use App\Traits\SiteTrait;
use App\Traits\User\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingFeatureService
{
    use SiteTrait, UserTrait;

    public function attachListingFeature(Listing $listing, Feature $feature) {
        $listing->features()->attach($feature->id);
        return true;
    }

    public function detachListingFeature(Listing $listing, Feature $feature) {
        $listingFeature = $listing->features()->where('feature_id', $feature->id)->first();
        if (!$listingFeature) {
            throw new \Exception('Listing feature not found');
        }
        return  $listingFeature->delete();
    }

}
