<?php

namespace App\Services\Feature;

use App\Models\Feature;
use App\Services\BaseService;

class FeatureService extends BaseService
{

    public function createFeature(array $data) {
        $feature = new Feature($data);
        if (!$feature->save()) {
            throw new \Exception('Error creating listing feature');
        }
        return true;
    }
    public function updateFeature(Feature $feature, array $data) {
        if (!$feature->update($data)) {
            throw new \Exception('Error updating listing feature');
        }
        return true;
    }

    public function deleteFeature(Feature $feature) {
        if (!$feature->delete()) {
            throw new \Exception('Error deleting listing feature');
        }
        return true;
    }

}
