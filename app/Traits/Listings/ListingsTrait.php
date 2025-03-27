<?php

namespace App\Traits\Listings;

use App\Enums\Listing\ListingFetchProperty;

trait ListingsTrait
{
    public function buildListingsQuery($query, array $data) {
        foreach ($data as $key => $value) {
            $query = $this->buildPropertyQuery($query, $key, $value);
        }
        return $query;
    }

    public function buildPropertyQuery($query, string $property, mixed $data) {
        $getProperty = ListingFetchProperty::tryFrom($property);
        if ($getProperty === null) {
            return $query;
        }
        switch ($getProperty) {
            case ListingFetchProperty::QUERY:
                return $query->where('title', 'like', '%' . $data . '%')
                    ->orWhere('description', 'like', '%' . $data . '%');
            case ListingFetchProperty::ID:
                return $query->where('id', $data);
            case ListingFetchProperty::TITLE:
                return $query->where('title', 'like', '%' . $data . '%');
            case ListingFetchProperty::DESCRIPTION:
                return $query->where('description', 'like', '%' . $data . '%');
            case ListingFetchProperty::PRICE:
                return $query->where('price', $data['value']);
            case ListingFetchProperty::USER:
                return $query->whereRelation('user', 'id', $data);
            case ListingFetchProperty::CREATED_AT:
                return $query->where('created_at', $data);
            case ListingFetchProperty::UPDATED_AT:
                return $query->where('updated_at', $data);
            case ListingFetchProperty::TYPE:
                return $query->whereRelation('type', 'slug', $data);
            case ListingFetchProperty::CATEGORIES:
                return $query->whereRelation('categories', 'slug', $data);
            case ListingFetchProperty::IMAGES:
            case ListingFetchProperty::VIEWS:
            case ListingFetchProperty::STATUS:
            case ListingFetchProperty::LOCATION:
            case ListingFetchProperty::LATITUDE:
            case ListingFetchProperty::LONGITUDE:
            case ListingFetchProperty::COUNTRY:
            case ListingFetchProperty::CITY:
            case ListingFetchProperty::PROXIMITY:
                return $query;
            default:
                return $query;
        }
    }

}