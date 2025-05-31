<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\ProductMedia;
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
    private ProductMedia $productMedia;
    private array $errors = [];


    public function __construct(Request $request, ImageUploadService $imageUploadService)
    {
        $this->request = $request;
        $this->imageUploadService = $imageUploadService;
    }

    public function createProductMedia(array $data = [])
    {
        $this->productMedia = new ProductMedia();
        return $this->saveProductMedia($data);
    }

    public function updateProductMedia(array $data = [])
    {
        try {
            return $this->saveProductMedia($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function deleteProductMedia()
    {
        try {
            $deleteFile = $this->deleteProductMediaFile();
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

    public function saveProductMedia(array $data = [])
    {
        try {
            $this->productMedia->fill($data);
            $saveProductMedia = $this->productMedia->save();
            if (!$saveProductMedia) {
                $this->addError('Error saving product media', $data);
                return false;
            }
            return $this->storeProductMediaUpload($data);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    private function getProductMediaUploadKey(string $type)
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

    private function getProductMediaUploadPath(array $data = [])
    {
        $product = $this->productMedia->product()->first();
        $getUploadKey = $this->getProductMediaUploadKey($data['type']);
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

    public static function getProductMediaUploadUrl(ProductMedia $productMedia)
    {
        switch ($productMedia->filesystem) {
            case 'local' :
                return self::getProductMediaLocalUrl($productMedia);
            case 'external_link' :
                return $productMedia->url;
            default:
                return false;
        }
    }
    public static function getProductMediaLocalUrl(ProductMedia $productMedia)
    {
        switch ($productMedia->category) {
            case 'product_image' :
            case 'product_video' :
                return url("{$productMedia->path}");
            default:
                return false;
        }
    }

    public function deleteProductMediaFile()
    {
        $fileSystem = $this->productMedia->filesystem;
        switch ($fileSystem) {
            case 'local':
                return $this->deleteLocalProductMediaFile();
            case 'external_link':
            default:
                return true;
        }
    }

    public function deleteLocalProductMediaFile()
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

    public function storeProductMediaUpload(array $data = [])
    {
        try {
            $name = "{$this->productMedia->category}__{$this->productMedia->id}";
            $path = $this->getProductMediaUploadPath($data);
            $key = $this->getProductMediaUploadKey($data['type']);
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
            $saveProductMedia = $this->productMedia->save();
            if (!$saveProductMedia) {
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
     * @return ProductMedia
     */
    public function getProductMedia(): ProductMedia
    {
        return $this->productMedia;
    }

    /**
     * @param ProductMedia $productMedia
     */
    public function setProductMedia(ProductMedia $productMedia): self
    {
        $this->productMedia = $productMedia;
        return $this;
    }

}
