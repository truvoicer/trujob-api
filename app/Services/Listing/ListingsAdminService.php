<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingMedia;
use App\Models\User;
use App\Repositories\ListingRepository;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingsAdminService
{

    private User $user;

    private Listing $listing;
    private ListingMedia $listingMedia;
    private array $errors = [];


    public function __construct(
        private ListingsMediaService $listingsMediaService,
        private ListingRepository $listingRepository
    )
    {
    }

    public function initializeListing() {
        $this->listing = new Listing(['active' => false]);
        $createListing = $this->user->listing()->save($this->listing);
        if (!$createListing) {
            $this->addError('Error initialising listing for user');
            return false;
        }
        return $this->saveListingRelations([]);
    }
    public function saveListing(?array $data = []) {
        if (!$this->listing->exists) {
            return $this->createListing($data);
        } else {
            return  $this->updateListing($data);
        }
    }
    public function createListing(array $data) {
        $slug = Str::slug($data['title']);
        $data['slug'] = $this->listingRepository->buildCloneEntityStr(
            $this->user->listing()->where('slug', $slug),
            'slug',
            $slug,
            '-'
        );

        $this->listing = new Listing($data);
        $createListing = $this->user->listing()->save($this->listing);
        if (!$createListing) {
            $this->addError('Error creating listing for user', $data);
            return false;
        }
        return $this->saveListingRelations($data);
    }

    public function updateListing(array $data) {
        $this->listing->fill($data);
        $saveListing = $this->listing->save();
        if (!$saveListing) {
            $this->addError('Error saving listing', $data);
            return false;
        }
        return $this->saveListingRelations($data);
    }

    public function deleteListing() {
        if (!$this->listing->delete()) {
            $this->addError('Error deleting listing');
            return false;
        }
        return true;
    }

    public function saveListingRelations(array $data) {
        try {
            if (isset($data['features']) && is_array($data['features'])) {
                $saveFeatures = $this->listing->listingFeature()->saveMany(
                    array_map(function ($feature) {
                        return new ListingFeature($feature);
                    }, $data['features'])
                );
                if (!$saveFeatures) {
                    $this->addError('Error saving features', $data);
                    return false;
                }
            }

            if (isset($data['hasImages']) && (bool)$data['hasImages']) {
                $imageData = $this->buildImageRequestData($data);
                foreach ($imageData as $image) {
                    $this->createListingMedia($image);
                }
            }
            return true;
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function buildImageRequestData(array $data) {
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
    public function createListingMedia(array $data = []) {
        if (!$this->listing->exists) {
            $listing = $this->createListing([]);
            if (!$listing) {
                return false;
            }
        }
        $this->listingMedia = new ListingMedia($data);
        return $this->saveListingMedia($data);
    }

    public function saveListingMedia(array $data = []) {
        try {
            $saveListingMedia = $this->listing->listingMedia()->save($this->listingMedia);
            if (!$saveListingMedia) {
                $this->addError('Error saving listing media', $data);
                return false;
            }
            $this->listingsMediaService->setListingMedia($this->listingMedia);
            $storeListingMedia = $this->listingsMediaService->saveListingMedia($data, $this->listing);
            if (!$storeListingMedia) {
                $this->setErrors(array_merge($this->errors, $this->listingsMediaService->getErrors()));
            }
            return $storeListingMedia;
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
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

}
