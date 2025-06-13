<?php

namespace App\Services\Product;

use App\Models\Media;
use App\Models\Product;
use App\Services\BaseService;

class ProductMediaService extends BaseService
{
     public function attachBulkMediasToProduct(Product $product, array $medias) {
        $product->media()->attach($medias);
        return true;
    }
    public function attachMediaToProduct(Product $product, Media $media) {
        $product->media()->attach($media->id);
        return true;
    }

    public function detachMediaFromProduct(Product $product, Media $media) {
        $productMedia = $product->media()->where('media_id', $media->id)->first();
        if (!$productMedia) {
            throw new \Exception('Product media not found');
        }
        return $productMedia->delete();
    }

    public function detachBulkMediasFromProduct(Product $product, array $medias) {
        $product->media()->detach($medias);
        return true;
    }

}
