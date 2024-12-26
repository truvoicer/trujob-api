<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Services\BaseService;
use App\Services\FetchService;

class ListingsFetchService extends BaseService
{
    use FetchService;


    public function listingsFetch()
    {
        $listing = Listing::query();
        if ($this->getPagination()) {
            return $listing->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $listing->get();
    }

    public function userListingsFetch()
    {
        $listing = $this->getUser()->listing();
        if ($this->getPagination()) {
            return $listing->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $listing->get();
    }

}
