<?php

namespace App\Services\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\Product;
use App\Models\MediaProduct;
use App\Models\Price;
use App\Models\ProductReview;
use App\Models\ProductType;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ProductAdminService extends BaseService
{

    public function __construct(
        private ProductMediaService $productsMediaService,
        private ProductRepository $productRepository
    ) {}

    public function getProductById(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            throw new \Exception('Product not found');
        }
        return $product;
    }

    public function initializeProduct()
    {
        $product = new Product(['active' => false]);
        $createProduct = $this->user->products()->save($product);
        if (!$createProduct) {
            throw new \Exception('Error creating product');
        }
        return $this->saveProductRelations($product, []);
    }
    public function saveProduct(Product $product, ?array $data = [])
    {
        if (!$product->exists) {
            return $this->createProduct($data);
        } else {
            return  $this->updateProduct($product, $data);
        }
    }
    public function createProduct(array $data)
    {

        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['title']);
        }
        $data['name'] = $this->productRepository->buildCloneEntityStr(
            $this->user->products()->where('name', $data['name']),
            'name',
            $data['name'],
            '-'
        );

        $product = new Product($data);
        $createProduct = $this->user->products()->save($product);
        if (!$createProduct) {
            throw new \Exception('Error creating product');
        }
        return $this->saveProductRelations($product, $data);
    }

    public function updateProduct(Product $product, array $data)
    {
        if (!$product->update($data)) {
            throw new \Exception('Error updating product');
        }
        return $this->saveProductRelations($product, $data);
    }

    public function deleteProduct(Product $product)
    {
        if (!$product->delete()) {
            throw new \Exception('Error deleting product');
        }
        return true;
    }

    public function saveProductRelations(Product $product, array $data)
    {
        try {
            if (!empty($data['type']) && is_int($data['type'])) {
                $type = ProductType::where('id', $data['type'])->first();
                if (!$type) {
                    throw new \Exception('Error saving product type');
                }
                $product->types()->attach($type);
            }
            if (isset($data['features']) && is_array($data['features'])) {
                $featureIds = array_map(function ($feature) {
                    return Feature::where('id', $feature)->first()?->id;
                }, $data['features']);
                $saveFeatures = $product->features()->sync(array_filter($featureIds));
            }

            if (isset($data['follows']) && is_array($data['follows'])) {
                $followIds = array_map(function ($follow) {
                    return User::where('id', $follow)->first()?->id;
                }, $data['follows']);
                $saveFollows = $product->follows()->sync(array_filter($followIds));
            }
            //brands, colors, product types, categories, reviews
            if (isset($data['brands']) && is_array($data['brands'])) {
                $brandIds = array_map(function ($brand) {
                    return Brand::where('id', $brand)->first()?->id;
                }, $data['brands']);
                $saveBrands = $product->brands()->sync(array_filter($brandIds));
            }
            if (isset($data['colors']) && is_array($data['colors'])) {
                $colorIds = array_map(function ($color) {
                    return Color::where('id', $color)->first()?->id;
                }, $data['colors']);
                $saveColors = $product->colors()->sync(array_filter($colorIds));
            }
            if (isset($data['product_types']) && is_array($data['product_types'])) {
                $productTypeIds = array_map(function ($productType) {
                    return ProductType::where('id', $productType)->first()?->id;
                }, $data['product_types']);
                $saveProductTypes = $product->productTypes()->sync(array_filter($productTypeIds));
            }
            if (isset($data['categories']) && is_array($data['categories'])) {
                $categoryIds = array_map(function ($category) {
                    return Category::where('id', $category)->first()?->id;
                }, $data['categories']);
                $saveCategories = $product->categories()->sync(array_filter($categoryIds));
            }
            if (isset($data['prices']) && is_array($data['prices'])) {
                $priceIds = array_map(function ($price) {
                    return Price::where('id', $price)->first()?->id;
                }, $data['prices']);
                $savePrices = $product->prices()->sync(array_filter($priceIds));
            }
            if (isset($data['reviews']) && is_array($data['reviews'])) {
                foreach ($data['reviews'] as $review) {
                    $productReview = new ProductReview($review);
                    $productReview->user_id = $this->user->id;
                    $productReview->product_id = $product->id;
                    if (!$productReview->save()) {
                        throw new \Exception('Error saving product review');
                    }
                }
            }

            if (!empty($data['media']) && is_array($data['media'])) {
                $imageData = $this->buildImageRequestData($data);
                foreach ($imageData as $image) {
                    $this->createMediaProduct($image);
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
            foreach (ProductMediaService::MEDIA_UPLOAD_FIELDS as $field) {
                if (isset($data["{$field}_{$step}"])) {
                    $imageData[$field] = $data["{$field}_{$step}"];
                }
            }
            $buildImageData[] = $imageData;
        }
        return $buildImageData;
    }
    public function createMediaProduct(Product $product, array $data = [])
    {
        if (!$product->exists) {
            $product = $this->createProduct([]);
            if (!$product) {
                return false;
            }
        }
        $productMedia = new MediaProduct($data);
        return $this->saveMediaProduct($product, $data);
    }

    public function saveMediaProduct(Product $product, array $data = [])
    {
        return true;
        // try {
        //     $saveMediaProduct = $product->productMedia()->save($productMedia);
        //     if (!$saveMediaProduct) {
        //         $this->addError('Error saving product media', $data);
        //         return false;
        //     }
        //     $productsMediaService->setMediaProduct($productMedia);
        //     $storeMediaProduct = $productsMediaService->saveMediaProduct($data, $product);
        //     if (!$storeMediaProduct) {
        //         $this->setErrors(array_merge($this->errors, $productsMediaService->getErrors()));
        //     }
        //     return $storeMediaProduct;
        // } catch (\Exception $exception) {
        //     throw new \Exception($exception->getMessage());
        // }
    }

    public function bulkDeleteProducts(array $productIds)
    {
        $products = Product::whereIn('id', $productIds)->get();
        if ($products->isEmpty()) {
            throw new \Exception('No products found for deletion');
        }
        foreach ($products as $product) {
            if (!$this->deleteProduct($product)) {
                throw new \Exception('Error deleting product with ID: ' . $product->id);
            }
        }
        return true;
    }
}
