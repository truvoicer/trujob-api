<?php

namespace App\Repositories;

use App\Models\UserDiscountUsage;

class UserDiscountUsageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserDiscountUsage::class);
    }

    public function getModel(): UserDiscountUsage
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
