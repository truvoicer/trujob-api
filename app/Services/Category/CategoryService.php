<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Services\BaseService;

class CategoryService extends BaseService
{

    public function createCategory(array $data)
    {
        $category = new Category($data);
        if (!$category->save()) {
            throw new \Exception('Error creating product category');
        }
        return true;
    }

    public function updateCategory(Category $category, array $data)
    {
        if (!$category->update($data)) {
            throw new \Exception('Error updating product category');
        }
        return true;
    }

    public function deleteCategory(Category $category)
    {
        if (!$category->delete()) {
            throw new \Exception('Error deleting product category');
        }
        return true;
    }

}
