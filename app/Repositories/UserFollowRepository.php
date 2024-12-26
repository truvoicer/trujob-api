<?php

namespace App\Repositories;

use App\Models\UserFollow;

class UserFollowRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserFollow::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserFollow
    {
        return parent::getModel();
    }


}
