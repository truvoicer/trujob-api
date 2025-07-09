<?php

namespace App\Repositories;

use App\Models\TransactionAmount;

class TransactionAmountRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(TransactionAmount::class);
    }

    public function getModel(): TransactionAmount
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
