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
            throw new \Exception('Error creating listing category');
        }
        return true;
    }

    public function updateCategory(Category $category, array $data)
    {
        if (!$category->update($data)) {
            throw new \Exception('Error updating listing category');
        }
        return true;
    }

    public function deleteCategory(Category $category)
    {
        if (!$category->delete()) {
            throw new \Exception('Error deleting listing category');
        }
        return true;
    }

}
