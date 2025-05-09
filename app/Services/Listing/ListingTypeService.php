<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingType;
use App\Services\BaseService;

class ListingTypeService extends BaseService
{

    public function createListingType(array $data)
    {
        $listingType = new ListingType();
        $listingType->fill($data);

        if (!$listingType->save()) {
            throw new \Exception('Error saving listing feature');
        }
        return true;
    }

    public function updateListingType(ListingType $listingType, array $data)
    {
        if (!$listingType->update($data)) {
            throw new \Exception('Error updating listing feature');
        }
        return true;
    }

    public function deleteListingType(ListingType $listingType)
    {
        if (!$listingType->delete()) {
            throw new \Exception('Error deleting listing feature');
        }
        return true;
    }

    public function attachListingTypeToListing(Listing $listing, ListingType $listingType)
    {
        $listing->listingTypes()->attach($listingType->id);
        return true;
    }

    public function detachListingTypeFromListing(Listing $listing, ListingType $listingType)
    {
        $listingListingType = $listing->listingTypes()->where('listing_type_id', $listingType->id)->first();
        if (!$listingListingType) {
            throw new \Exception('Listing listingType not found');
        }
        return $listingListingType->delete();
    }
}
