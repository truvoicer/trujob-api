<?php

namespace App\Repositories;

use App\Models\ListingFeature;

class ListingFeatureRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingFeature::class);
    }

    public function getModel(): ListingFeature
    {
        return parent::getModel();
    }

    public function findByParams(string $sort, string  $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function findByQuery($query)
    {
        return $this->findAll();
    }

}
