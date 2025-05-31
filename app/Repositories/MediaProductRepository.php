<?php

namespace App\Repositories;

use App\Models\MediaProduct;

class MediaProductRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(MediaProduct::class);
    }

    public function getModel(): MediaProduct
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
