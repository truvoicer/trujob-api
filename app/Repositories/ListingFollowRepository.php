<?php

namespace App\Repositories;

use App\Models\ListingFollow;

class ListingFollowRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingFollow::class);
    }

    public function getModel(): ListingFollow
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
