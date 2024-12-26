<?php

namespace App\Repositories;

use App\Models\FirebaseTopic;

class FirebaseTopicRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(FirebaseTopic::class);
    }

    public function getModel(): FirebaseTopic
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
