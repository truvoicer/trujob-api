<?php

namespace App\Services\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Services\BaseService;
use App\Services\FetchService;

class ProductBrandService extends BaseService
{
    public function attachBulkBrandsToProduct(Product $product, array $brands) {
        $product->brands()->attach($brands);
        return true;
    }

    public function attachBrandToProduct(Product $product, Brand $brand) {
        $product->brands()->attach($brand->id);
        return true;
    }

    public function detachBrandFromProduct(Product $product, Brand $brand) {
        $productBrand = $product->brands()->where('brand_id', $brand->id)->first();
        if (!$productBrand) {
            throw new \Exception('Product brand not found');
        }
        return $productBrand->delete();
    }

    public function detachBulkBrandsFromProduct(Product $product, array $brands) {
        $product->brands()->detach($brands);
        return true;
    }

}
