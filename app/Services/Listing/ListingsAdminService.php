<?php

namespace App\Services\Listing;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingMedia;
use App\Models\ListingReview;
use App\Models\ListingType;
use App\Models\Order;
use App\Models\ProductType;
use App\Models\User;
use App\Repositories\ListingRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ListingsAdminService extends BaseService
{

    public function __construct(
        private ListingsMediaService $listingsMediaService,
        private ListingRepository $listingRepository
    ) {}

    public function getListingById(int $id)
    {
        $listing = Listing::find($id);
        if (!$listing) {
            throw new \Exception('Listing not found');
        }
        return $listing;
    }

    public function initializeListing()
    {
        $listing = new Listing(['active' => false]);
        $createListing = $this->user->listings()->save($listing);
        if (!$createListing) {
            throw new \Exception('Error creating listing');
        }
        return $this->saveListingRelations($listing, []);
    }
    public function saveListing(Listing $listing, ?array $data = [])
    {
        if (!$listing->exists) {
            return $this->createListing($data);
        } else {
            return  $this->updateListing($listing, $data);
        }
    }
    public function createListing(array $data)
    {

        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['title']);
        }
        $data['name'] = $this->listingRepository->buildCloneEntityStr(
            $this->user->listings()->where('name', $data['name']),
            'name',
            $data['name'],
            '-'
        );

        $listing = new Listing($data);
        $createListing = $this->user->listings()->save($listing);
        if (!$createListing) {
            throw new \Exception('Error creating listing');
        }
        return $this->saveListingRelations($listing, $data);
    }

    public function updateListing(Listing $listing, array $data)
    {
        if (!$listing->update($data)) {
            throw new \Exception('Error updating listing');
        }
        return $this->saveListingRelations($listing, $data);
    }

    public function deleteListing(Listing $listing)
    {
        if (!$listing->delete()) {
            throw new \Exception('Error deleting listing');
        }
        return true;
    }

    public function saveListingRelations(Listing $listing, array $data)
    {
        try {
            if (!empty($data['type']) && is_int($data['type'])) {
                $type = ListingType::where('id', $data['type'])->first();
                if (!$type) {
                    throw new \Exception('Error saving listing type');
                }
                $listing->types()->attach($type);
            }
            if (isset($data['features']) && is_array($data['features'])) {
                $featureIds = array_map(function ($feature) {
                    return Feature::where('id', $feature)->first()?->id;
                }, $data['features']);
                $saveFeatures = $listing->features()->sync(array_filter($featureIds));
            }

            if (isset($data['follows']) && is_array($data['follows'])) {
                $followIds = array_map(function ($follow) {
                    return User::where('id', $follow)->first()?->id;
                }, $data['follows']);
                $saveFollows = $listing->follows()->sync(array_filter($followIds));
            }
            //brands, colors, product types, categories, reviews
            if (isset($data['brands']) && is_array($data['brands'])) {
                $brandIds = array_map(function ($brand) {
                    return Brand::where('id', $brand)->first()?->id;
                }, $data['brands']);
                $saveBrands = $listing->brands()->sync(array_filter($brandIds));
            }
            if (isset($data['colors']) && is_array($data['colors'])) {
                $colorIds = array_map(function ($color) {
                    return Color::where('id', $color)->first()?->id;
                }, $data['colors']);
                $saveColors = $listing->colors()->sync(array_filter($colorIds));
            }
            if (isset($data['product_types']) && is_array($data['product_types'])) {
                $productTypeIds = array_map(function ($productType) {
                    return ProductType::where('id', $productType)->first()?->id;
                }, $data['product_types']);
                $saveProductTypes = $listing->productTypes()->sync(array_filter($productTypeIds));
            }
            if (isset($data['categories']) && is_array($data['categories'])) {
                $categoryIds = array_map(function ($category) {
                    return Category::where('id', $category)->first()?->id;
                }, $data['categories']);
                $saveCategories = $listing->categories()->sync(array_filter($categoryIds));
            }
            if (isset($data['reviews']) && is_array($data['reviews'])) {
                foreach ($data['reviews'] as $review) {
                    $listingReview = new ListingReview($review);
                    $listingReview->user_id = $this->user->id;
                    $listingReview->listing_id = $listing->id;
                    if (!$listingReview->save()) {
                        throw new \Exception('Error saving listing review');
                    }
                }
            }

            if (!empty($data['media']) && is_array($data['media'])) {
                $imageData = $this->buildImageRequestData($data);
                foreach ($imageData as $image) {
                    $this->createListingMedia($image);
                }
            }
            return true;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function buildImageRequestData(array $data)
    {
        $buildImageData = [];
        $images = [];
        foreach ($data as $key => $item) {
            $needle = 'image_';
            $pos = strpos($key, 'image_');
            if ($pos === false) {
                continue;
            }
            $images[] = (int)substr($key, $pos + strlen($needle));
        }
        foreach ($images as $step) {
            $imageData = [];
            foreach (ListingsMediaService::MEDIA_UPLOAD_FIELDS as $field) {
                if (isset($data["{$field}_{$step}"])) {
                    $imageData[$field] = $data["{$field}_{$step}"];
                }
            }
            $buildImageData[] = $imageData;
        }
        return $buildImageData;
    }
    public function createListingMedia(Listing $listing, array $data = [])
    {
        if (!$listing->exists) {
            $listing = $this->createListing([]);
            if (!$listing) {
                return false;
            }
        }
        $listingMedia = new ListingMedia($data);
        return $this->saveListingMedia($listing, $data);
    }

    public function saveListingMedia(Listing $listing, array $data = [])
    {
        return true;
        // try {
        //     $saveListingMedia = $listing->listingMedia()->save($listingMedia);
        //     if (!$saveListingMedia) {
        //         $this->addError('Error saving listing media', $data);
        //         return false;
        //     }
        //     $listingsMediaService->setListingMedia($listingMedia);
        //     $storeListingMedia = $listingsMediaService->saveListingMedia($data, $listing);
        //     if (!$storeListingMedia) {
        //         $this->setErrors(array_merge($this->errors, $listingsMediaService->getErrors()));
        //     }
        //     return $storeListingMedia;
        // } catch (\Exception $exception) {
        //     throw new \Exception($exception->getMessage());
        // }
    }

    public function createOrderItem(Order $order, Listing $listing, array $data = [])
    {
        if (!$listing->exists()) {
            throw new \Exception('Listing does not exist');
        }
        $data['order_id'] = $order->id;
        return $listing->orderItems()->create($data);
    }
}
