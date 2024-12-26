<?php

namespace App\Services\Listing;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\FetchService;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingBrandService
{
    use FetchService;

    private User $user;
    private Request $request;

    private Listing $listing;
    private ListingBrand $listingBrand;
    private Brand $brand;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function brandFetch()
    {
        $brand = Brand::query();
        if ($this->getPagination()) {
            return $brand->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $brand->get();
    }

    public function addBrandToListing() {
        $this->listingBrand = new ListingBrand();
        $this->listingBrand->brand_id = $this->brand->id;
        $create = $this->listing->listingBrand()->save($this->listingBrand);
        if (!$create) {
            $this->addError('Error creating listing brand for user');
            return false;
        }
        return true;
    }
    public function removeBrandFromListing() {
        $this->listingBrand = new ListingBrand();
        $this->listingBrand->brand_id = $this->brand->id;
        $this->listingBrand->listing_id = $this->listing->id;
        $delete = $this->listing->listingBrand()->delete($this->listingBrand);
        if (!$delete) {
            $this->addError('Error deleting listing brand for user');
            return false;
        }
        return true;
    }

    public function createBrand(array $data) {
        $this->brand = new Brand($data);
        $save = $this->brand->save();
        if (!$save) {
            $this->addError('Error saving listing brand', $data);
            return false;
        }
        return true;
    }
    public function updateBrand(array $data) {
        $this->brand->fill($data);
        $save = $this->brand->save();
        if (!$save) {
            $this->addError('Error saving listing brand', $data);
            return false;
        }
        return true;
    }

    public function deleteBrand() {
        if (!$this->listingBrand->delete()) {
            $this->addError('Error deleting listing brand');
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
     * @param ListingBrand $listingBrand
     */
    public function setListingBrand(ListingBrand $listingBrand): void
    {
        $this->listingBrand = $listingBrand;
    }

    /**
     * @return ListingBrand
     */
    public function getListingBrand(): ListingBrand
    {
        return $this->listingBrand;
    }

    /**
     * @return Brand
     */
    public function getBrand(): Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     */
    public function setBrand(Brand $brand): void
    {
        $this->brand = $brand;
    }

}
