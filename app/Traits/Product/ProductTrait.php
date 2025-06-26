<?php

namespace App\Traits\Product;

use App\Enums\Product\ProductFetchProperty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ProductTrait
{
    public function buildProductsQuery($query, array $data)
    {
        foreach ($data as $key => $value) {
            $query = $this->buildPropertyQuery($query, $key, $value);
        }
        return $query;
    }

    public function buildPropertyQuery(Builder $query, string $property, mixed $data)
    {
        $getProperty = ProductFetchProperty::tryFrom($property);
        if ($getProperty === null) {
            return $query;
        }
        switch ($getProperty) {
            case ProductFetchProperty::QUERY:
                return $query->where('title', 'like', '%' . $data . '%')
                    ->orWhere('description', 'like', '%' . $data . '%');
            case ProductFetchProperty::ID:
                return $query->where('id', $data);
            case ProductFetchProperty::TITLE:
                return $query->where('title', 'like', '%' . $data . '%');
            case ProductFetchProperty::DESCRIPTION:
                return $query->where('description', 'like', '%' . $data . '%');
            case ProductFetchProperty::PRICE:
                return $query->where('price', $data['value']);
            case ProductFetchProperty::USER:
                return $query->whereRelation('user', 'id', $data);
            case ProductFetchProperty::CREATED_AT:
                return $query->where('created_at', $data);
            case ProductFetchProperty::UPDATED_AT:
                return $query->where('updated_at', $data);
            case ProductFetchProperty::TYPE:
                return $query->whereHas('productCategories', function ($query) use ($data) {
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
            case ProductFetchProperty::CATEGORIES:
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
            case ProductFetchProperty::IMAGES:
            case ProductFetchProperty::VIEWS:
            case ProductFetchProperty::STATUS:
            case ProductFetchProperty::LOCATION:
            case ProductFetchProperty::LATITUDE:
            case ProductFetchProperty::LONGITUDE:
            case ProductFetchProperty::COUNTRY:
            case ProductFetchProperty::CITY:
            case ProductFetchProperty::PROXIMITY:
                return $query;
            default:
                return $query;
        }
    }
}
