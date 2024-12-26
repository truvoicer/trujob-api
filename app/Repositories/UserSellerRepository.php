<?php

namespace App\Repositories;

use App\Models\UserSeller;

class UserSellerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserSeller::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserSeller
    {
        return parent::getModel();
    }

}
