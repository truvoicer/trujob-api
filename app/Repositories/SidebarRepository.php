<?php

namespace App\Repositories;

use App\Models\Sidebar;
use App\Models\Site;

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

    public function findBySite(Site $site)
    {
        $this->setQuery($site->sidebars());
        $this->setWith([
            'sidebarWidgets' => function ($query) {
                $query->orderBy('order', 'asc');
            },
        ]);
        return $this->findMany();
    }

    public function findSidebarWidgets(Sidebar $sidebar)
    {
        $this->setQuery($sidebar->sidebarWidgets());
        $this->setOrderDir('asc');
        $this->setSortField('order');
        return $this->findMany();
    }
}
