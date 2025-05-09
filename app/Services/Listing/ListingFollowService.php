<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFollow;
use App\Services\BaseService;

class ListingFollowService extends BaseService
{

    public function createListingFollow(Listing $listing, array $data) {
        $listingFollow = new ListingFollow($data);
        $listingFollow->listing_id = $listing->id;
        if (!$listing->listingFollow()->save($listingFollow)) {
            throw new \Exception('Error creating listing follow');
        }
        return true;
    }

    public function updateListingFollow(ListingFollow $listingFollow, array $data) {
        if (!$listingFollow->update($data)) {
            throw new \Exception('Error updating listing follow');
        }
        return true;
    }

    public function deleteListingFollow(ListingFollow $listingFollow) {
        if (!$listingFollow->delete()) {
            throw new \Exception('Error deleting listing follow');
        }
        return true;
    }

}
