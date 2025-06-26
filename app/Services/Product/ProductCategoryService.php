<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\BaseService;

class ProductCategoryService extends BaseService
{
    public function attachBulkCategoriesToProduct(Product $product, array $categories) {
        $product->categories()->attach($categories);
        return true;
    }

    public function attachCategoryToProduct(Product $product, ProductCategory $category) {
        $product->categories()->attach($category->id);
        return true;
    }

    public function detachCategoryFromProduct(Product $product, ProductCategory $category) {
        $productCategory = $product->categories()->where('category_id', $category->id)->first();
        if (!$productCategory) {
            throw new \Exception('Product category not found');
        }
        return $productCategory->delete();
    }
    public function detachBulkCategoriesFromProduct(Product $product, array $categories) {
        $product->categories()->detach($categories);
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
