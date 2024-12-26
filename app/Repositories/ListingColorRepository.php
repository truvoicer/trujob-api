<?php

namespace App\Repositories;

use App\Models\ListingColor;

class ListingColorRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingColor::class);
    }

    public function getModel(): ListingColor
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
