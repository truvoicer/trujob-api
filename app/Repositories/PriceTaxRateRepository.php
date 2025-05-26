<?php

namespace App\Repositories;

use App\Models\PriceTaxRate;

class PriceTaxRateRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PriceTaxRate::class);
    }

    public function getModel(): PriceTaxRate
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
