<?php

namespace App\Services\Listing;

use App\Models\Category;
use App\Models\Listing;
use App\Services\BaseService;

class ListingCategoryService extends BaseService
{

    public function attachCategoryToListing(Listing $listing, Category $category) {
        $listing->categories()->attach($category->id);
        return true;
    }

    public function detachCategoryFromListing(Listing $listing, Category $category) {
        $listingCategory = $listing->categories()->where('category_id', $category->id)->first();
        if (!$listingCategory) {
            throw new \Exception('Listing category not found');
        }
        return $listingCategory->delete();
    }

}
