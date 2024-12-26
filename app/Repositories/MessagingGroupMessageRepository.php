<?php

namespace App\Repositories;

use App\Models\MessagingGroupMessage;

class MessagingGroupMessageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(MessagingGroupMessage::class);
    }

    public function getModel(): MessagingGroupMessage
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
