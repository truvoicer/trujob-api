<?php

namespace App\Services\Discount;

use App\Factories\Product\ProductFactory;
use App\Helpers\ProductHelpers;
use App\Models\Discount;
use App\Models\Price;
use App\Services\BaseService;
use Illuminate\Support\Str;

class DiscountService extends BaseService
{

    public function createDiscount(array $data)
    {
        $products = $data['products'] ?? null;
        $prices = $data['prices'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['products'], $data['prices'], $data['category_ids']);
        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['label']);
        }
        $discount = new Discount($data);
        if (!$discount->save()) {
            throw new \Exception('Error creating discount');
        }
        if ($products && is_array($products)) {
            $this->saveProducts($discount, $products);
        }
        if ($prices && is_array($prices)) {
            $this->savePrices($discount, $prices);
        }
        if ($categoryIds && is_array($categoryIds)) {
            $discount->categories()->sync($categoryIds);
        }
        $this->relatedData($discount, $data);
        return true;
    }
    public function updateDiscount(Discount $discount, array $data)
    {
        $products = $data['products'] ?? null;
        $prices = $data['prices'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['products'], $data['prices'], $data['category_ids']);
        if (!$discount->update($data)) {
            throw new \Exception('Error updating discount');
        }
        if ($products && is_array($products)) {
            $this->saveProducts($discount, $products);
        }
        if ($prices && is_array($prices)) {
            $this->savePrices($discount, $prices);
        }
        if ($categoryIds && is_array($categoryIds)) {
            $discount->categories()->sync($categoryIds);
        }
        $this->relatedData($discount, $data);
        return true;
    }

    public function updateDefaultTaxRate(Discount $discount, array $data): void
    {
        if (array_key_exists('is_default', $data)  && $data['is_default']) {
            $this->setAsDefault($discount);
        } else if (array_key_exists('is_default', $data) && !$data['is_default']) {
            $this->removeAsDefault($discount);
        }
    }

    public function setAsDefault(Discount $discount): void
    {
        if (!$discount->default) {
            $discount->default()->create();
        }
    }
    public function removeAsDefault(Discount $discount): void
    {
        if ($discount->default) {
            $discount->default()->delete();
        }
    }
    public function relatedData(Discount $discount, array $data)
    {
        $this->updateDefaultTaxRate($discount, $data);
        return $discount;
    }



    public function saveProducts(Discount $discount, array $productData)
    {
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

    public function savePrices(Discount $discount, array $priceData)
    {
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
    public function deleteDiscount(Discount $discount)
    {
        if (!$discount->delete()) {
            throw new \Exception('Error deleting discount');
        }
        return true;
    }
}
