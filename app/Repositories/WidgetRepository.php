<?php

namespace App\Repositories;

use App\Models\Widget;

class WidgetRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Widget::class);
    }

    public function getModel(): Widget
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

    public function findBySite($site)
    {
        $this->setQuery($site->widgets());
        return $this->findMany();
    }

}
