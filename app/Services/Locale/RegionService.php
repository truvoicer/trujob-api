<?php

namespace App\Services\Locale;

use App\Models\Region;
use App\Services\BaseService;

class RegionService extends BaseService
{

    public function createRegionBatch(array $data) {
        $createRegionBatch = Region::create($data['countries']);
        if (!$createRegionBatch) {
            throw new \Exception('Error creating region batch');
        }
        return true;
    }
    public function createRegion(array $data) {
        $region = new Region($data);
        if (!$region->save()) {
            throw new \Exception('Error creating region');
        }
        return true;
    }

    public function updateRegion(Region $region, array $data) {
        if (!$region->update($data)) {
            throw new \Exception('Error updating region');
        }
        return true;
    }

    public function deleteRegion(Region $region) {
        if (!$region->delete()) {
            throw new \Exception('Error deleting region');
        }
        return true;
    }


}
