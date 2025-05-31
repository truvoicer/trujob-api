<?php

namespace App\Services\Product;

use App\Models\Feature;
use App\Models\Product;
use App\Services\BaseService;

class ProductFeatureService extends BaseService
{

    public function attachFeatureToProduct(Product $product, Feature $feature) {
        $product->features()->attach($feature->id);
        return true;
    }

    public function detachFeatureFromProduct(Product $product, Feature $feature) {
        $productFeature = $product->features()->where('feature_id', $feature->id)->first();
        if (!$productFeature) {
            throw new \Exception('Product feature not found');
        }
        return $productFeature->delete();
    }

}
