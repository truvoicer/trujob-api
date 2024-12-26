<?php

namespace App\Repositories;

use App\Models\ListingReview;

class ListingReviewRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingReview::class);
    }

    public function getModel(): ListingReview
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
