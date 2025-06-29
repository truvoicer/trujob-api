<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\BaseService;

class ProductCategoryService extends BaseService
{
    public function attachBulkProductCategoriesToProduct(Product $product, array $productCategories) {
        $product->productCategories()->attach($productCategories);
        return true;
    }

    public function attachProductCategoryToProduct(Product $product, ProductCategory $productCategory) {
        $product->productCategories()->attach($productCategory->id);
        return true;
    }

    public function detachProductCategoryFromProduct(Product $product, ProductCategory $productCategory) {
        $productCategory = $product->productCategories()->where('category_id', $productCategory->id)->first();
        if (!$productCategory) {
            throw new \Exception('Product category not found');
        }
        return $productCategory->delete();
    }
    public function detachBulkProductCategoriesFromProduct(Product $product, array $productCategories) {
        $product->productCategories()->detach($productCategories);
        return true;
    }

    public function createProductCategory(array $data) {
        $productCategory = new ProductCategory($data);
        if (!$productCategory->save()) {
            throw new \Exception('Error creating product product category');
        }
        return true;
    }
    public function updateProductCategory(ProductCategory $productCategory, array $data) {
        if (!$productCategory->update($data)) {
            throw new \Exception('Error updating product product category');
        }
        return true;
    }

    public function deleteProductCategory(ProductCategory $productCategory) {
        if (!$productCategory->delete()) {
            throw new \Exception('Error deleting product product category');
        }
        return true;
    }
}
