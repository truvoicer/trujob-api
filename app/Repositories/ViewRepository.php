<?php

namespace App\Repositories;

use App\Models\View;

class ViewRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(View::class);
    }

    public function getModel(): View
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
