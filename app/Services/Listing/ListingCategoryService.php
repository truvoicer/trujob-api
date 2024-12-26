<?php

namespace App\Services\Listing;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\FetchService;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingCategoryService
{
    use FetchService;

    private User $user;

    private Category $category;
    private Listing $listing;
    private ListingCategory $listingCategory;
    private array $errors = [];

    public function categoryFetch()
    {
        $category = Category::query();
        if ($this->getPagination()) {
            return $category->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $category->get();
    }

    public function addCategoryToListing()
    {
        $this->listingCategory = new ListingCategory();
        $this->listingCategory->category_id = $this->category->id;
        $create = $this->listing->listingCategory()->save($this->listingCategory);
        if (!$create) {
            $this->addError('Error creating listing category for user');
            return false;
        }
        return true;
    }

    public function removeCategoryFromListing()
    {
        $this->listingCategory = new ListingCategory();
        $this->listingCategory->category_id = $this->category->id;
        $this->listingCategory->listing_id = $this->listing->id;
        $delete = $this->listing->listingCategory()->delete($this->listingCategory);
        if (!$delete) {
            $this->addError('Error deleting listing category for user');
            return false;
        }
        return true;
    }

    public function createCategory(array $data)
    {
        $this->category = new Category($data);
        $save = $this->category->save();
        if (!$save) {
            $this->addError('Error saving listing category', $data);
            return false;
        }
        return true;
    }

    public function updateCategory(array $data)
    {
        $this->category->fill($data);
        $save = $this->category->save();
        if (!$save) {
            $this->addError('Error saving category', $data);
            return false;
        }
        return true;
    }

    public function deleteCategory()
    {
        if (!$this->category->delete()) {
            $this->addError('Error deleting category');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Listing
     */
    public function getListing(): Listing
    {
        return $this->listing;
    }

    /**
     * @param Listing $listing
     */
    public function setListing(Listing $listing): void
    {
        $this->listing = $listing;
    }

    /**
     * @param ListingCategory $listingCategory
     */
    public function setListingCategory(ListingCategory $listingCategory): void
    {
        $this->listingCategory = $listingCategory;
    }

    /**
     * @return ListingCategory
     */
    public function getListingCategory(): ListingCategory
    {
        return $this->listingCategory;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }


}
