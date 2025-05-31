<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductReview;
use App\Services\BaseService;

class ProductReviewService extends BaseService
{

    public function createproductReview(Product $product, array $data) {
        $productReview = new productReview($data);
        if (!$product->productReview()->save($productReview)) {
            throw new \Exception('Error creating product review');
        }
        return true;
    }

    public function updateproductReview(ProductReview $productReview, array $data) {
        if (!$productReview->update($data)) {
            throw new \Exception('Error updating product review');
        }
        return true;
    }

    public function deleteproductReview(ProductReview $productReview) {
        if (!$productReview->delete()) {
            throw new \Exception('Error deleting product review');
        }
        return true;
    }

}
