<?php

namespace App\Services\Listing;

use App\Enums\Listing\ListingFetchProperty;
use App\Helpers\Tools\UtilHelpers;
use App\Http\Requests\Listing\ListingFetchRequest;
use App\Models\Listing;
use App\Services\BaseService;
use App\Services\FetchService;
use App\Traits\Listings\ListingsTrait;

class ListingsFetchService extends BaseService
{
    use FetchService, ListingsTrait;

    public function handleRequest(ListingFetchRequest $request): array
    {
        $requestData = $request->validated();
        if (!empty($requestData[ListingFetchProperty::CATEGORIES->value])) {
            $requestData[ListingFetchProperty::CATEGORIES->value] = UtilHelpers::stringToArray(
                $requestData[ListingFetchProperty::CATEGORIES->value]
            );
        }
        if (!empty($requestData[ListingFetchProperty::TYPE->value])) {
            $requestData[ListingFetchProperty::TYPE->value] = UtilHelpers::stringToArray(
                $requestData[ListingFetchProperty::TYPE->value]
            );
        }
        if (!empty($requestData[ListingFetchProperty::USER->value])) {
            $requestData[ListingFetchProperty::USER->value] = UtilHelpers::stringToArray(
                $requestData[ListingFetchProperty::USER->value]
            );
        }
        return $requestData;
    }

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
