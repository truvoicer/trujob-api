<?php

namespace App\Repositories;

use App\Models\FirebaseDevice;

class FirebaseDeviceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(FirebaseDevice::class);
    }

    public function getModel(): FirebaseDevice
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
