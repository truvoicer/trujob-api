<?php

namespace App\Repositories;

use App\Models\ListingType;

class ListingTypeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingType::class);
    }

    public function getModel(): ListingType
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
