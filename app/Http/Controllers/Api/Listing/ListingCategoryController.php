<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingCategoryRequest;
use App\Http\Requests\Listing\UpdateListingCategoryRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Services\Listing\ListingCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingCategoryController extends Controller
{
    protected ListingCategoryService $listingCategoryService;

    public function __construct(ListingCategoryService $listingCategoryService, Request $request)
    {
        $this->listingCategoryService = $listingCategoryService;
    }

    public function addCategoryToListing(Listing $listing, Category $category, Request $request) {
        $this->listingCategoryService->setUser($request->user());
        $this->listingCategoryService->setListing($listing);
        $this->listingCategoryService->setCategory($category);
        $addCategory = $this->listingCategoryService->addCategoryToListing();
        if (!$addCategory) {
            return $this->sendErrorResponse(
                'Error adding listing category',
                [],
                $this->listingCategoryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Added listing category', [], $this->listingCategoryService->getErrors());
    }

    public function removeCategoryFromListing(Listing $listing, Category $category, Request $request) {
        $this->listingCategoryService->setUser($request->user());
        $this->listingCategoryService->setListing($listing);
        $this->listingCategoryService->setCategory($category);
        $removeCategoryFromListing = $this->listingCategoryService->removeCategoryFromListing();
        if (!$removeCategoryFromListing) {
            return $this->sendErrorResponse(
                'Error removing listing category',
                [],
                $this->listingCategoryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Removed listing category', [], $this->listingCategoryService->getErrors());
    }
}
