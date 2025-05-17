<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Listing\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{

    public function __construct(
        private CategoryService $categoryService,
        private CategoryRepository $categoryRepository,
    )
    {
    }

    public function index(Request $request) {

        $this->categoryRepository->setPagination(true);
        $this->categoryRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->categoryRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->categoryRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->categoryRepository->setPage(
            $request->get('page', 1)
        );
        
        return CategoryResource::collection(
            $this->categoryRepository->findMany()
        );
    }

    public function create(StoreCategoryRequest $request) {
        $this->categoryService->setUser($request->user()->user);
        $this->categoryService->setSite($request->user()->site);

        $create = $this->categoryService->createCategory($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating category',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Category created',
        ], Response::HTTP_CREATED);
    }

    public function update(Category $category, UpdateCategoryRequest $request) {
        $this->categoryService->setUser($request->user()->user);
        $this->categoryService->setSite($request->user()->site);
        
        $update = $this->categoryService->updateCategory($category, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating category',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Category updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Category $category, Request $request) {
        $this->categoryService->setUser($request->user()->user);
        $this->categoryService->setSite($request->user()->site);

        $delete = $this->categoryService->deleteCategory($category);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting category',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Category deleted',
        ], Response::HTTP_OK);
    }
}
