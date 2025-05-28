<?php

namespace App\Repositories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Region::class);
    }

    public function getModel(): Region
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

    public function getActiveRegions(): Collection
    {
        return Region::with(['country'])
            ->active()
            ->get();
    }

    public function getByCountry(int $countryId): Collection
    {
        return Region::with(['country'])
            ->forCountry($countryId)
            ->active()
            ->get();
    }
}
