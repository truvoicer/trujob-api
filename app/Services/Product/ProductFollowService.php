<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductFollow;
use App\Models\User;
use App\Services\BaseService;

class ProductFollowService extends BaseService
{

    public function createProductFollow(Product $product, array $userIds) {
        foreach ($userIds as $userId) {
            $user = $this->site->users()->find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }
            $productFollow = new ProductFollow();
            $productFollow->product_id = $product->id;
            $productFollow->user_id = $user->id;
            if (!$product->productFollow()->save($productFollow)) {
                throw new \Exception('Error creating product follow');
            }
        }
        return true;
    }

    public function updateProductFollow(ProductFollow $productFollow, array $data) {
        if (!$productFollow->update($data)) {
            throw new \Exception('Error updating product follow');
        }
        return true;
    }

    public function deleteProductFollow(ProductFollow $productFollow) {
        if (!$productFollow->delete()) {
            throw new \Exception('Error deleting product follow');
        }
        return true;
    }

    public function detachBulkFollowsFromProduct(Product $product, array $userIds) {
        foreach ($userIds as $userId) {
            $productFollow = $product->productFollow()->where('user_id', $userId)->first();
            if (!$productFollow) {
                throw new \Exception('Product follow not found for user id: ' . $userId);
            }
            if (!$productFollow->delete()) {
                throw new \Exception('Error deleting product follow for user id: ' . $userId);
            }
        }
        return true;
    }
}
