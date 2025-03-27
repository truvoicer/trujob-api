<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Services\BaseService;
use App\Services\FetchService;
use App\Traits\Listings\ListingsTrait;

class ListingsFetchService extends BaseService
{
    use FetchService, ListingsTrait;


    public function listingsFetch(?array $data = [])
    {
        $listing = $this->buildListingsQuery(Listing::query(), $data);
        
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

    public function userListingsFetch(?array $data = [])
    {
        $listing = $this->getUser()->listing();
        
        $listing = $this->buildListingsQuery($this->getUser()->listing(), $data);
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
