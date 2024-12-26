<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Event;

class EventRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Event::class);
    }

    public function getModel(): Event
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
