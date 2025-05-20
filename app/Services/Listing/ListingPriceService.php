<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingPrice;
use App\Models\Price;
use App\Services\BaseService;

class ListingPriceService extends BaseService
{

    public function createlistingPrice(Listing $listing, array $data) {
        $listingPrice = new Price($data);
        if (!$listingPrice->save()) {
            throw new \Exception('Error creating listing price');
        }
        $listing->prices()->attach($listingPrice->id);
        return true;
    }

    public function updatelistingPrice(Listing $listing, Price $price, array $data) {
        $listingPrice = $listing->prices()->find($price->id);
        if (!$listingPrice) {
            throw new \Exception('Listing price not found');
        }
        if (!$listingPrice->update($data)) {
            throw new \Exception('Error updating listing price');
        }
        return true;
    }

    public function deletelistingPrice(Listing $listing, Price $price) {
        $listingPrice = $listing->prices()->find($price->id);
        if (!$listingPrice) {
            throw new \Exception('Listing price not found');
        }
        if (!$listing->prices()->detach($listingPrice)) {
            throw new \Exception('Error detaching listing price');
        }
        return true;
    }

}
