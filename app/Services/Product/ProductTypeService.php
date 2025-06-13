<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductType;
use App\Services\BaseService;

class ProductTypeService extends BaseService
{
    public function attachBulkTypesToProduct(Product $product, array $typeIds): bool
    {
        $product->productTypes()->attach($typeIds);
        return true;
    }

    public function detachBulkTypesFromProduct(Product $product, array $typeIds): bool
    {
        $product->productTypes()->detach($typeIds);
        return true;
    }

    public function createProductType(array $data)
    {
        $productType = new ProductType();
        $productType->fill($data);

        if (!$productType->save()) {
            throw new \Exception('Error saving product feature');
        }
        return true;
    }

    public function updateProductType(ProductType $productType, array $data)
    {
        if (!$productType->update($data)) {
            throw new \Exception('Error updating product feature');
        }
        return true;
    }

    public function deleteProductType(ProductType $productType)
    {
        if (!$productType->delete()) {
            throw new \Exception('Error deleting product feature');
        }
        return true;
    }

    public function attachProductTypeToProduct(Product $product, ProductType $productType)
    {
        $product->productTypes()->attach($productType->id);
        return true;
    }

    public function detachProductTypeFromProduct(Product $product, ProductType $productType)
    {
        $productProductType = $product->productTypes()->where('product_type_id', $productType->id)->first();
        if (!$productProductType) {
            throw new \Exception('Product productType not found');
        }
        return $productProductType->delete();
    }
}
