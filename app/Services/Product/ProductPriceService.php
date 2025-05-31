<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Price;
use App\Services\BaseService;

class ProductPriceService extends BaseService
{

    public function createproductPrice(Product $product, array $data) {
        $productPrice = new Price($data);
        if (!$productPrice->save()) {
            throw new \Exception('Error creating product price');
        }
        $product->prices()->attach($productPrice->id);
        return true;
    }

    public function updateproductPrice(Product $product, Price $price, array $data) {
        $productPrice = $product->prices()->find($price->id);
        if (!$productPrice) {
            throw new \Exception('Product price not found');
        }
        if (!$productPrice->update($data)) {
            throw new \Exception('Error updating product price');
        }
        return true;
    }

    public function deleteproductPrice(Product $product, Price $price) {
        $productPrice = $product->prices()->find($price->id);
        if (!$productPrice) {
            throw new \Exception('Product price not found');
        }
        if (!$product->prices()->detach($productPrice)) {
            throw new \Exception('Error detaching product price');
        }
        return true;
    }

}
