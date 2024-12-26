<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\CategoryCollection;
use App\Models\Category;
use App\Services\Listing\ListingCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    protected ListingCategoryService $listingCategoryService;

    public function __construct(ListingCategoryService $listingCategoryService, Request $request)
    {
        $this->listingCategoryService = $listingCategoryService;
    }

    public function fetchCategories(Request $request) {
        $this->listingCategoryService->setPagination(true);
        return $this->sendSuccessResponse(
            'Category created',
            ( new CategoryCollection($this->listingCategoryService->categoryFetch())),
            $this->listingCategoryService->getErrors());
    }

    public function createCategory(Request $request) {
        $this->listingCategoryService->setUser($request->user());
        $create = $this->listingCategoryService->createCategory($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating category',
                [],
                $this->listingCategoryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Category created', [], $this->listingCategoryService->getErrors());
    }

    public function updateCategory(Category $category, Request $request) {
        $this->listingCategoryService->setUser($request->user());
        $this->listingCategoryService->setCategory($category);
        $update = $this->listingCategoryService->updateCategory($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating category',
                [],
                $this->listingCategoryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Category updated', [], $this->listingCategoryService->getErrors());
    }
    public function deleteCategory(Category $category) {
        $this->listingCategoryService->setCategory($category);
        $delete = $this->listingCategoryService->deleteCategory();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting category',
                [],
                $this->listingCategoryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Category deleted', [], $this->listingCategoryService->getErrors());
    }
}
