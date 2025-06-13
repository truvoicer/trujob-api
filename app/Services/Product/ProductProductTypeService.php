<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductType;
use App\Services\BaseService;

class ProductProductTypeService extends BaseService
{

    public function attachBulkProductTypesToProduct(Product $product, array $productTypes) {
        $product->productTypes()->attach($productTypes);
        return true;
    }
    public function detachBulkProductTypesFromProduct(Product $product, array $productTypes) {
        $product->productTypes()->detach($productTypes);
        return true;
    }

    public function attachProductTypeToProduct(Product $product, ProductType $productType) {
        $product->productTypes()->attach($productType->id);
        return true;
    }

    public function detachProductTypeFromProduct(Product $product, ProductType $productType) {
        $productProductType = $product->productTypes()->where('productType_id', $productType->id)->first();
        if (!$productProductType) {
            throw new \Exception('Product productType not found');
        }
        return $productProductType->delete();
    }

}
