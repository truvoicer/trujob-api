<?php

namespace App\Repositories;

use App\Models\PriceType;

class PriceTypeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PriceType::class);
    }

    public function getModel(): PriceType
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
