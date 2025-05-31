<?php

namespace App\Services\Product;

use App\Models\Color;
use App\Models\Product;
use App\Services\BaseService;

class ProductColorService extends BaseService
{

    public function attachColorToProduct(Product $product, Color $color) {
        $product->colors()->attach($color->id);
        return true;
    }

    public function detachColorFromProduct(Product $product, Color $color) {
        $productColor = $product->colors()->where('color_id', $color->id)->first();
        if (!$productColor) {
            throw new \Exception('Product color not found');
        }
        return $productColor->delete();
    }
}
