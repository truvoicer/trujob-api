<?php

namespace App\Repositories;

use App\Models\Site;

class SiteRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Site::class);
    }

    public function getModel(): Site
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
