<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingsMediaService
{
    const MEDIA_UPLOAD_FIELDS = [
        'category',
        'image',
        'type',
        'alt',
        'filesystem'
    ];

    private User $user;
    private Request $request;
    private ImageUploadService $imageUploadService;
    private ListingMedia $listingMedia;
    private array $errors = [];


    public function __construct(Request $request, ImageUploadService $imageUploadService)
    {
        $this->request = $request;
        $this->imageUploadService = $imageUploadService;
    }

    public function createListingMedia(array $data = [])
    {
        $this->listingMedia = new ListingMedia();
        return $this->saveListingMedia($data);
    }

    public function updateListingMedia(array $data = [])
    {
        try {
            return $this->saveListingMedia($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function deleteListingMedia()
    {
        try {
            $deleteFile = $this->deleteListingMediaFile();
            if (!$deleteFile) {
                $this->addError('Error deleting file');
            }
            if (!$this->listingMedia->delete()) {
                $this->addError('Error deleting listing');
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function saveListingMedia(array $data = [])
    {
        try {
            $this->listingMedia->fill($data);
            $saveListingMedia = $this->listingMedia->save();
            if (!$saveListingMedia) {
                $this->addError('Error saving listing media', $data);
                return false;
            }
            return $this->storeListingMediaUpload($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    private function getListingMediaUploadKey(string $type)
    {
        if (!isset($type)) {
            $this->addError('Type is missing from listing media', [$type]);
            return false;
        }
        switch ($type) {
            case 'image' :
                return 'image';
            case 'video' :
                return 'video';
            default:
                $this->addError('Invalid listing media type', [$type]);
                return false;
        }
    }

    private function getListingMediaUploadPath(array $data = [])
    {
        $listing = $this->listingMedia->listing()->first();
        $getUploadKey = $this->getListingMediaUploadKey($data['type']);
        if (!$getUploadKey) {
            return false;
        }

        if (!isset($data['category'])) {
            $this->addError('Category is missing from listing media', $data);
            return false;
        }
        $path = ['media', 'listing', $listing->id, $getUploadKey];
        switch ($data['category']) {
            case 'listing_image' :
                $path[] = 'listing_image';
                break;
            case 'listing_video' :
                $path[] = 'listing_video';
                break;
            default:
                $this->addError('Invalid listing media category', $data);
                return false;
        }
        return implode('/', $path);
    }

    public static function getListingMediaUploadUrl(ListingMedia $listingMedia)
    {
        switch ($listingMedia->filesystem) {
            case 'local' :
                return self::getListingMediaLocalUrl($listingMedia);
            case 'external_link' :
                return $listingMedia->url;
            default:
                return false;
        }
    }
    public static function getListingMediaLocalUrl(ListingMedia $listingMedia)
    {
        switch ($listingMedia->category) {
            case 'listing_image' :
            case 'listing_video' :
                return url("{$listingMedia->path}");
            default:
                return false;
        }
    }

    public function deleteListingMediaFile()
    {
        $fileSystem = $this->listingMedia->filesystem;
        switch ($fileSystem) {
            case 'local':
                return $this->deleteLocalListingMediaFile();
            case 'external_link':
            default:
                return true;
        }
    }

    public function deleteLocalListingMediaFile()
    {
        $path = $this->listingMedia->path;
        if (!$path) {
            $this->addError('File path is not set');
            return false;
        }
        $filePath = public_path($path);
        if (!Storage::fileExists($filePath)) {
            $this->addError('File does not exist');
            return false;
        }
        return Storage::delete($filePath);
    }

    public function storeListingMediaUpload(array $data = [])
    {
        try {
            $name = "{$this->listingMedia->category}__{$this->listingMedia->id}";
            $path = $this->getListingMediaUploadPath($data);
            $key = $this->getListingMediaUploadKey($data['type']);
            if (!$path || !$key) {
                return false;
            }
            $storeImage = $this->imageUploadService->imageUpload($data['image'], $path, $name);
            if (!$storeImage) {
                $this->addError('Error uploading listing media', $data);
                return false;
            }
            $data = [
                'path' => $storeImage
            ];

            $this->listingMedia->fill($data);
            $saveListingMedia = $this->listingMedia->save();
            if (!$saveListingMedia) {
                $this->addError('Error saving listing media', $data);
                return false;
            }
            return true;
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
     * @return ListingMedia
     */
    public function getListingMedia(): ListingMedia
    {
        return $this->listingMedia;
    }

    /**
     * @param ListingMedia $listingMedia
     */
    public function setListingMedia(ListingMedia $listingMedia): self
    {
        $this->listingMedia = $listingMedia;
        return $this;
    }

}
