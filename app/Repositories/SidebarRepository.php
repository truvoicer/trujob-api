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
        return $this->findMany();
    }

    public function findSidebarWidgets(Sidebar $sidebar)
    {
        $this->setQuery($sidebar->widgets());
        return $this->findMany();
    }

}
