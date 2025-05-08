<?php

namespace App\Services\Listing;

use App\Models\ListingType;
use App\Traits\SiteTrait;
use App\Traits\User\UserTrait;

class ListingTypeService
{
    use SiteTrait, UserTrait;

    public function createListingType(array $data) {
        $listingType = new ListingType();
        $listingType->fill($data);

        if (!$listingType->save()) {
            throw new \Exception('Error saving listing feature');
        }
        return true;
    }

    public function updateListingType(ListingType $listingType, array $data) {
        if (!$listingType->update($data)) {
            throw new \Exception('Error updating listing feature');
        }
        return true;
    }

    public function deleteListingType(ListingType $listingType) {
        if (!$listingType->delete()) {
            throw new \Exception('Error deleting listing feature');
        }
        return true;
    }


}
