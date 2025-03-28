<?php

namespace App\Traits\Listings;

use App\Enums\Listing\ListingFetchProperty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ListingsTrait
{
    public function buildListingsQuery($query, array $data)
    {
        foreach ($data as $key => $value) {
            $query = $this->buildPropertyQuery($query, $key, $value);
        }
        return $query;
    }

    public function buildPropertyQuery(Builder $query, string $property, mixed $data)
    {
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
                return $query->whereHas('listingType', function ($query) use ($data) {
                    if (is_array($data)) {
                        if (count(array_filter($data, 'is_numeric')) === count($data)) {
                            $query->whereIn('id', $data);
                        } else if (count(array_filter($data, 'is_string')) === count($data)) {
                            $query->whereIn('name', $data);
                        }
                    } else if (is_string($data)) {
                        $query->where('name', $data);
                    } else if (is_int($data)) {
                        $query->where('id', $data);
                    }
                });
                return $query;
            case ListingFetchProperty::CATEGORIES:
                return $query->whereHas('categories', function ($query) use ($data) {
                    if (is_array($data)) {
                        if (count(array_filter($data, 'is_numeric')) === count($data)) {
                            $query->whereIn('id', $data);
                        } else if (count(array_filter($data, 'is_string')) === count($data)) {
                            $query->whereIn('name', $data);
                        }
                    } else if (is_string($data)) {
                        $query->where('name', $data);
                    } else if (is_int($data)) {
                        $query->where('id', $data);
                    }
                });
                return $query;
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
