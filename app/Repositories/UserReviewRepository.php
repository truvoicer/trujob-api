<?php

namespace App\Repositories;

use App\Models\UserReview;

class UserReviewRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserReview::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserReview
    {
        return parent::getModel();
    }

}
