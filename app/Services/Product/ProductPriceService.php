<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\Price;
use App\Services\BaseService;

class ProductPriceService extends BaseService
{

    public function createproductPrice(Product $product, array $data)
    {
        $taxRateIds = $data['tax_rate_ids'] ?? null;
        unset($data['tax_rate_ids']);
        $discountIds = $data['discount_ids'] ?? null;
        unset($data['discount_ids']);

        $productPrice = new Price($data);
        if (!$productPrice->save()) {
            throw new \Exception('Error creating product price');
        }
        $product->prices()->attach($productPrice->id);

        $this->updateDefaults($product, $productPrice, $data);

        if (is_array($taxRateIds)) {
            $this->syncTaxRateIds($productPrice, $taxRateIds);
        }
        if (is_array($discountIds)) {
            $this->syncDiscounts($productPrice, $discountIds);
        }

        return true;
    }

    public function updateproductPrice(Product $product, Price $price, array $data)
    {
        $productPrice = $product->prices()->find($price->id);
        if (!$productPrice) {
            throw new \Exception('Product price not found');
        }

        $taxRateIds = $data['tax_rate_ids'] ?? null;
        unset($data['tax_rate_ids']);
        $discountIds = $data['discount_ids'] ?? null;
        unset($data['discount_ids']);

        if (!$productPrice->update($data)) {
            throw new \Exception('Error updating product price');
        }

        $this->updateDefaults($product, $price, $data);
        if (is_array($taxRateIds)) {
            $this->syncTaxRateIds($productPrice, $taxRateIds);
        }
        if (is_array($discountIds)) {
            $this->syncDiscounts($productPrice, $discountIds);
        }
        return true;
    }


    public function updateDefaults(Product $product, Price $price, array $data)
    {
        if (!empty($data['is_default'])) {
            $product->prices()->where('prices.id', '!=', $price->id)->update(['is_default' => false]);
        }
    }

    public function syncTaxRateIds(Price $price, array $ids): void
    {
        $price->taxRates()->sync($ids);
    }

    public function syncDiscounts(Price $price, array $ids): void
    {
        $price->discounts()->sync($ids);
    }

    public function deleteproductPrice(Product $product, Price $price)
    {
        $productPrice = $product->prices()->find($price->id);
        if (!$productPrice) {
            throw new \Exception('Product price not found');
        }
        if (!$product->prices()->detach($productPrice)) {
            throw new \Exception('Error detaching product price');
        }
        return true;
    }

    public function attachBulkPricesToProduct(Product $product, array $prices) {
        $product->prices()->attach($prices);
        return true;
    }

    public function detachBulkPricesFromProduct(Product $product, array $prices) {
        $product->prices()->detach($prices);
        return true;
    }
}
