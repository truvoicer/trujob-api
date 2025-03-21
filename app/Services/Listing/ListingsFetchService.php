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
            $results = $listing->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getPage() ?? null
            );
            $this->setTotal($results->total());
            return $results;
        }
        $results = $listing->get();
        $this->setTotal($results->count());
        return $results;
    }

    public function userListingsFetch()
    {
        $listing = $this->getUser()->listing();
        
        if ($this->getPagination()) {
            $results = $listing->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getPage() ?? null
            );
            $this->setTotal($results->total());
            return $results;
        }
        $results = $listing->get();
        $this->setTotal($results->count());
        return $results;
    }

}
