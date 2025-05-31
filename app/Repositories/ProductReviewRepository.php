<?php

namespace App\Repositories;

use App\Models\ProductReview;

class ProductReviewRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductReview::class);
    }

    public function getModel(): ProductReview
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
