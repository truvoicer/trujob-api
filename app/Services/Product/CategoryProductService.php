<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\Category;
use App\Services\BaseService;

class CategoryProductService extends BaseService
{
    public function attachBulkCategoriesToProduct(Product $product, array $categories) {
        $product->categories()->attach($categories);
        return true;
    }

    public function attachCategoryToProduct(Product $product, Category $category) {
        $product->categories()->attach($category->id);
        return true;
    }

    public function detachCategoryFromProduct(Product $product, Category $category) {
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

}
