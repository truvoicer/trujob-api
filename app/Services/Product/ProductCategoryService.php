<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\Services\BaseService;

class ProductCategoryService extends BaseService
{

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

}
