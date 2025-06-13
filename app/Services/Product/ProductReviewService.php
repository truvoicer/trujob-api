<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductReview;
use App\Services\BaseService;

class ProductReviewService extends BaseService
{
    public function attachBulkReviewsToProduct(Product $product, array $productReviews) {
        $product->productReview()->createMany($productReviews);
        return true;
    }

    public function detachBulkReviewsFromProduct(Product $product, array $productReviewIds) {
        $product->productReview()->whereIn('id', $productReviewIds)->delete();
        return true;
    }

    public function createProductReview(Product $product, array $data) {
        $productReview = new ProductReview($data);
        if (!$product->productReview()->save($productReview)) {
            throw new \Exception('Error creating product review');
        }
        return true;
    }

    public function updateProductReview(ProductReview $productReview, array $data) {
        if (!$productReview->update($data)) {
            throw new \Exception('Error updating product review');
        }
        return true;
    }

    public function deleteProductReview(ProductReview $productReview) {
        if (!$productReview->delete()) {
            throw new \Exception('Error deleting product review');
        }
        return true;
    }

}
