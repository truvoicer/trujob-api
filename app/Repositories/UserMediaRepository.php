<?php

namespace App\Repositories;

use App\Models\UserMedia;

class UserMediaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserMedia::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserMedia
    {
        return parent::getModel();
    }

}
