<?php

namespace App\Repositories;

use App\Models\ListingMedia;

class ListingMediaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingMedia::class);
    }

    public function getModel(): ListingMedia
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
