<?php

namespace App\Repositories;

use App\Models\Listing;

class ListingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Listing::class);
    }

    public function getModel(): Listing
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
