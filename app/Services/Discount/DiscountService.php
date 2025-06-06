<?php

namespace App\Services\Discount;

use App\Factories\Product\ProductFactory;
use App\Helpers\ProductHelpers;
use App\Models\Discount;
use App\Models\Price;
use App\Services\BaseService;

class DiscountService extends BaseService
{

    public function createDiscount(array $data) {
        $discount = new Discount($data);
        if (!$discount->save()) {
            throw new \Exception('Error creating discount');
        }
        $this->relatedData($discount, $data);
        return true;
    }
    public function updateDiscount(Discount $discount, array $data) {
        if (!$discount->update($data)) {
            throw new \Exception('Error updating discount');
        }
        $this->relatedData($discount, $data);
        return true;
    }

    public function updateDefaultTaxRate(Discount $discount, array $data): void
    {
        if (array_key_exists('is_default', $data)  && $data['is_default']) {
            $discount->default()->create();
        } else if (array_key_exists('is_default', $data) && !$data['is_default']) {
            $discount->default()->delete();
        }
    }
    public function relatedData(Discount $discount, array $data) {

        if (!empty($data['products']) && is_array($data['products'])) {
            $this->saveProducts($discount, $data['products']);
        }
        
        if (!empty($data['prices']) && is_array($data['prices'])) {
            $this->savePrices($discount, $data['prices']);
        }

        if (!empty($data['category_ids']) && is_array($data['category_ids'])) {
            $discount->categories()->sync($data['category_ids']);
        }
        $this->updateDefaultTaxRate($discount, $data);
        return $discount;
    }

    public function saveProducts(Discount $discount, array $productData) {
        $products = [];
        foreach ($productData as $data) {
            $product = ProductFactory::create(
            ProductHelpers::validateProductableByArray('product_type', $data)
        )
            ->attachDiscountRelations(
                $discount,
                $data
            );
            if (!$product->save()) {
                throw new \Exception('Error saving product');
            }
            $products[] = $product;
        }
        return $products;
    }

    public function savePrices(Discount $discount, array $priceData) {
        $products = [];
        foreach ($priceData as $data) {
            $price = null;
            if (is_int($data)) {
                $price = Price::find($data);
                if (!$price) {
                    throw new \Exception('Price not found');
                }
            } else if ($data instanceof Price) {
                $price = $data;
            } 
            if (!$price instanceof Price) {
                throw new \Exception('Price not provided');
            }
            $discount->prices()->attach($price->id);
        }
        return $products;
    }
    public function deleteDiscount(Discount $discount) {
        if (!$discount->delete()) {
            throw new \Exception('Error deleting discount');
        }
        return true;
    }

}
