<?php

namespace App\Repositories;

use App\Models\Sidebar;

class SidebarRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Sidebar::class);
    }

    public function getModel(): Sidebar
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
