<?php

namespace App\Repositories;

use App\Models\MessagingGroup;

class MessagingGroupRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(MessagingGroup::class);
    }

    public function getModel(): MessagingGroup
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
