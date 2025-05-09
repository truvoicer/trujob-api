<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingReview;
use App\Services\BaseService;

class ListingReviewService extends BaseService
{

    public function createlistingReview(Listing $listing, array $data) {
        $listingReview = new listingReview($data);
        if (!$listing->listingReview()->save($listingReview)) {
            throw new \Exception('Error creating listing review');
        }
        return true;
    }

    public function updatelistingReview(ListingReview $listingReview, array $data) {
        if (!$listingReview->update($data)) {
            throw new \Exception('Error updating listing review');
        }
        return true;
    }

    public function deletelistingReview(ListingReview $listingReview) {
        if (!$listingReview->delete()) {
            throw new \Exception('Error deleting listing review');
        }
        return true;
    }

}
