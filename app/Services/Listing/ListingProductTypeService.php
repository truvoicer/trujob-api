<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingProductType;
use App\Models\ListingMedia;
use App\Models\ProductType;
use App\Models\User;
use App\Services\FetchService;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingProductTypeService
{
    use FetchService;

    private User $user;
    private Request $request;

    private Listing $listing;
    private ListingProductType $listingProductType;
    private ProductType $productType;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function productTypeFetch()
    {
        $productType = ProductType::query();
        if ($this->getPagination()) {
            return $productType->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $productType->get();
    }
    public function addProductTypeToListing() {
        $this->listingProductType = new ListingProductType();
        $this->listingProductType->product_type_id = $this->productType->id;
        $this->listingProductType->listing_id = $this->listing->id;
        $create = $this->listing->listingProductType()->save($this->listingProductType);
        if (!$create) {
            $this->addError('Error creating listing product type for user');
            return false;
        }
        return true;
    }
    public function removeProductTypeFromListing() {
        $this->listingProductType = new ListingProductType();
        $this->listingProductType->product_type_id = $this->productType->id;
        $this->listingProductType->listing_id = $this->listing->id;
        $delete = $this->listing->listingProductType()->delete($this->listingProductType);
        if (!$delete) {
            $this->addError('Error deleting listing product type for user');
            return false;
        }
        return true;
    }

    public function createProductType(array $data) {
        $this->listingProductType = new ListingProductType($data);
        $saveListingProductType = $this->listingProductType->save();
        if (!$saveListingProductType) {
            $this->addError('Error saving listing product type', $data);
            return false;
        }
        return true;
    }
    public function updateProductType(array $data) {
        $this->listingProductType->fill($data);
        $saveListingProductType = $this->listingProductType->save();
        if (!$saveListingProductType) {
            $this->addError('Error saving listing product type', $data);
            return false;
        }
        return true;
    }

    public function deleteProductType() {
        if (!$this->listingProductType->delete()) {
            $this->addError('Error deleting listing product type');
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
     * @param ListingProductType $listingProductType
     */
    public function setListingProductType(ListingProductType $listingProductType): void
    {
        $this->listingProductType = $listingProductType;
    }

    /**
     * @return ListingProductType
     */
    public function getListingProductType(): ListingProductType
    {
        return $this->listingProductType;
    }

    /**
     * @return ProductType
     */
    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    /**
     * @param ProductType $productType
     */
    public function setProductType(ProductType $productType): void
    {
        $this->productType = $productType;
    }


}
