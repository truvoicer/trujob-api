<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFollow;
use App\Models\User;
use App\Services\BaseService;

class ListingFollowService extends BaseService
{

    public function createListingFollow(Listing $listing, array $userIds) {
        foreach ($userIds as $userId) {
            $user = $this->site->users()->find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }
            $listingFollow = new ListingFollow();
            $listingFollow->listing_id = $listing->id;
            $listingFollow->user_id = $user->id;
            if (!$listing->listingFollow()->save($listingFollow)) {
                throw new \Exception('Error creating listing follow');
            }
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
