<?php

namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(MenuItem::class);
    }

    public function getModel(): MenuItem
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
