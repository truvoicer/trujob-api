<?php

namespace App\Repositories;

use App\Models\FirebaseTopicDevice;

class FirebaseTopicDeviceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(FirebaseTopicDevice::class);
    }

    public function getModel(): FirebaseTopicDevice
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
