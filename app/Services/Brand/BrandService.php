<?php

namespace App\Services\Brand;

use App\Models\Brand;
use App\Services\BaseService;

class BrandService extends BaseService
{

    public function createBrand(array $data) {
        $brand = new Brand($data);
        if (!$brand->save()) {
            throw new \Exception('Error creating listing brand');
        }
        return true;
    }
    public function updateBrand(Brand $brand, array $data) {
        if (!$brand->update($data)) {
            throw new \Exception('Error updating listing brand');
        }
        return true;
    }

    public function deleteBrand(Brand $brand) {
        if (!$brand->delete()) {
            throw new \Exception('Error deleting listing brand');
        }
        return true;
    }

}
