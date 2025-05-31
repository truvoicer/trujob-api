<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\MediaProduct;
use App\Models\MediaProduct;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsMediaService
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
    private MediaProduct $productMedia;
    private array $errors = [];


    public function __construct(Request $request, ImageUploadService $imageUploadService)
    {
        $this->request = $request;
        $this->imageUploadService = $imageUploadService;
    }

    public function createMediaProduct(array $data = [])
    {
        $this->productMedia = new MediaProduct();
        return $this->saveMediaProduct($data);
    }

    public function updateMediaProduct(array $data = [])
    {
        try {
            return $this->saveMediaProduct($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function deleteMediaProduct()
    {
        try {
            $deleteFile = $this->deleteMediaProductFile();
            if (!$deleteFile) {
                $this->addError('Error deleting file');
            }
            if (!$this->productMedia->delete()) {
                $this->addError('Error deleting product');
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function saveMediaProduct(array $data = [])
    {
        try {
            $this->productMedia->fill($data);
            $saveMediaProduct = $this->productMedia->save();
            if (!$saveMediaProduct) {
                $this->addError('Error saving product media', $data);
                return false;
            }
            return $this->storeMediaProductUpload($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    private function getMediaProductUploadKey(string $type)
    {
        if (!isset($type)) {
            $this->addError('Type is missing from product media', [$type]);
            return false;
        }
        switch ($type) {
            case 'image' :
                return 'image';
            case 'video' :
                return 'video';
            default:
                $this->addError('Invalid product media type', [$type]);
                return false;
        }
    }

    private function getMediaProductUploadPath(array $data = [])
    {
        $product = $this->productMedia->product()->first();
        $getUploadKey = $this->getMediaProductUploadKey($data['type']);
        if (!$getUploadKey) {
            return false;
        }

        if (!isset($data['category'])) {
            $this->addError('Category is missing from product media', $data);
            return false;
        }
        $path = ['media', 'product', $product->id, $getUploadKey];
        switch ($data['category']) {
            case 'product_image' :
                $path[] = 'product_image';
                break;
            case 'product_video' :
                $path[] = 'product_video';
                break;
            default:
                $this->addError('Invalid product media category', $data);
                return false;
        }
        return implode('/', $path);
    }

    public static function getMediaProductUploadUrl(MediaProduct $productMedia)
    {
        switch ($productMedia->filesystem) {
            case 'local' :
                return self::getMediaProductLocalUrl($productMedia);
            case 'external_link' :
                return $productMedia->url;
            default:
                return false;
        }
    }
    public static function getMediaProductLocalUrl(MediaProduct $productMedia)
    {
        switch ($productMedia->category) {
            case 'product_image' :
            case 'product_video' :
                return url("{$productMedia->path}");
            default:
                return false;
        }
    }

    public function deleteMediaProductFile()
    {
        $fileSystem = $this->productMedia->filesystem;
        switch ($fileSystem) {
            case 'local':
                return $this->deleteLocalMediaProductFile();
            case 'external_link':
            default:
                return true;
        }
    }

    public function deleteLocalMediaProductFile()
    {
        $path = $this->productMedia->path;
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

    public function storeMediaProductUpload(array $data = [])
    {
        try {
            $name = "{$this->productMedia->category}__{$this->productMedia->id}";
            $path = $this->getMediaProductUploadPath($data);
            $key = $this->getMediaProductUploadKey($data['type']);
            if (!$path || !$key) {
                return false;
            }
            $storeImage = $this->imageUploadService->imageUpload($data['image'], $path, $name);
            if (!$storeImage) {
                $this->addError('Error uploading product media', $data);
                return false;
            }
            $data = [
                'path' => $storeImage
            ];

            $this->productMedia->fill($data);
            $saveMediaProduct = $this->productMedia->save();
            if (!$saveMediaProduct) {
                $this->addError('Error saving product media', $data);
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
     * @return MediaProduct
     */
    public function getMediaProduct(): MediaProduct
    {
        return $this->productMedia;
    }

    /**
     * @param MediaProduct $productMedia
     */
    public function setMediaProduct(MediaProduct $productMedia): self
    {
        $this->productMedia = $productMedia;
        return $this;
    }

}
