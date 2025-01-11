<?php

namespace App\Repositories;

use App\Models\AppMenu;

class AppMenuRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(AppMenu::class);
    }

    public function getModel(): AppMenu
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
