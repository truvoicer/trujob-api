<?php

namespace App\Repositories;

use App\Models\PaymentGateway;

class PaymentGatewayRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PaymentGateway::class);
    }

    public function getModel(): PaymentGateway
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
