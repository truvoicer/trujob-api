<?php

namespace App\Services\ProductType;

use App\Models\ProductType;
use App\Services\BaseService;

class ProductTypeService extends BaseService
{
    public function createProductType(array $data) {
        $productType = new ProductType($data);
        if (!$productType->save()) {
            throw new \Exception('Error creating product productType');
        }
        return true;
    }
    public function updateProductType(ProductType $productType, array $data) {
        if (!$productType->update($data)) {
            throw new \Exception('Error updating product productType');
        }
        return true;
    }

    public function deleteProductType(ProductType $productType) {
        if (!$productType->delete()) {
            throw new \Exception('Error deleting product productType');
        }
        return true;
    }

}
