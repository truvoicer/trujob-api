<?php

namespace App\Repositories;

use App\Models\SiteSetting;

class SiteSettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(SiteSetting::class);
    }

    public function getModel(): SiteSetting
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
