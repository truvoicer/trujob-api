<?php

namespace App\Repositories;

use App\Models\Feature;

class FeatureRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Feature::class);
    }

    public function getModel(): Feature
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
