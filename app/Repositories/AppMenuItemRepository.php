<?php

namespace App\Repositories;

use App\Models\AppMenuItem;

class AppMenuItemRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(AppMenuItem::class);
    }

    public function getModel(): AppMenuItem
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
