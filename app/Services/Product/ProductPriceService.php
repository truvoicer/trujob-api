<?php

namespace App\Services\Product;

use App\Enums\Price\PriceType;
use App\Models\Product;
use App\Models\Price;
use App\Models\PriceSubscription;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ProductPriceService extends BaseService
{

    public function createproductPrice(Product $product, array $data)
    {
        $taxRateIds = $data['tax_rate_ids'] ?? null;
        unset($data['tax_rate_ids']);
        $discountIds = $data['discount_ids'] ?? null;
        unset($data['discount_ids']);
        if (empty($data['valid_from'])) {
            $data['valid_from'] = now();
        }

        $hasPriceForCountryCurrency = $product->prices()
            ->where('price_type', $data['price_type'])
            ->where('currency_id', $data['currency_id'])
            ->where('country_id', $data['country_id'])
            ->exists();
        if ($hasPriceForCountryCurrency) {
            throw new \Exception('Product already has a price for this type, currency and country');
        }


        $productPrice = new Price($data);
        if (!$productPrice->save()) {
            throw new \Exception('Error creating product price');
        }
        $product->prices()->attach($productPrice->id);

        if (is_array($taxRateIds)) {
            $this->syncTaxRateIds($productPrice, $taxRateIds);
        }
        if (is_array($discountIds)) {
            $this->syncDiscounts($productPrice, $discountIds);
        }
        $priceType = PriceType::tryFrom($data['price_type']);
        if (!$priceType) {
            throw new \Exception('Invalid price type');
        }
        switch ($priceType) {
            case PriceType::SUBSCRIPTION:
                $this->createPriceSubscription($productPrice, $data);
                break;
                // Add more cases as needed
        }
        return true;
    }

    private function extractPriceSubscriptionFields(array $data): array
    {
        $newData = [];
        if (isset($data['price_id'])) {
            $newData['price_id'] = $data['price_id'];
        }
        if (isset($data['label'])) {
            $newData['label'] = $data['label'];
        }
        if (isset($data['description'])) {
            $newData['description'] = $data['description'];
        }
        if (isset($data['setup_fee']['value'])) {
            $newData['setup_fee_value'] = $data['setup_fee']['value'];
        }
        if (isset($data['setup_fee']['currency_id'])) {
            $newData['setup_fee_currency_id'] = $data['setup_fee']['currency_id'];
        }
        if (isset($data['name'])) {
            $newData['name'] = $data['name'];
        } else {
            $newData['name'] = Str::slug($data['label'] ?? 'Subscription', '-');
        }
        return $newData;
    }

    private function extractPriceSubscriptionItemFields(array $data): array
    {
        $newData = [];
        if (isset($data['price_subscription_id'])) {
            $newData['price_subscription_id'] = $data['price_subscription_id'];
        }
        if (isset($data['frequency']['interval_unit'])) {
            $newData['frequency_interval_unit'] = $data['frequency']['interval_unit'];
        }
        if (isset($data['frequency']['interval_count'])) {
            $newData['frequency_interval_count'] = $data['frequency']['interval_count'];
        }
        if (isset($data['tenure_type'])) {
            $newData['tenure_type'] = $data['tenure_type'];
        }
        if (isset($data['sequence'])) {
            $newData['sequence'] = $data['sequence'];
        }
        if (isset($data['total_cycles'])) {
            $newData['total_cycles'] = $data['total_cycles'];
        }
        if (isset($data['price']['value'])) {
            $newData['price_value'] = $data['price']['value'];
        }
        if (isset($data['price']['currency_id'])) {
            $newData['price_currency_id'] = $data['price']['currency_id'];
        }
        return $newData;
    }

    private function extractPriceSubscriptionItemsFields(array $data): array
    {
        return array_map(function ($item) {
            return $this->extractPriceSubscriptionItemFields($item);
        }, $data['items'] ?? []);
    }

    private function createPriceSubscription(Price $price, array $data)
    {
        $subscriptionData = $this->extractPriceSubscriptionFields($data);

        $subscriptionItemsData = $this->extractPriceSubscriptionItemsFields($data);

        $subscription = $price->subscription()->create($subscriptionData);
        if (isset($data['items']) && is_array($data['items'])) {
            $subscription->items()->createMany($subscriptionItemsData);
        }
        return $subscription;
    }
    private function updatePriceSubscription(PriceSubscription $priceSubscription, array $data)
    {

        $subscriptionData = $this->extractPriceSubscriptionFields($data);


        if (empty($subscriptionData)) {
            throw new \Exception('No valid subscription data provided');
        }
        $subscription = $priceSubscription->update($subscriptionData);
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $item = $this->extractPriceSubscriptionItemFields($item);
                $priceSubscription->items()->updateOrCreate(
                    $item,
                    $item
                );
            }
        }
        return $subscription;
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


        $hasPriceForCountryCurrency = $product->prices()
            ->where(
                'price_type',
                $data['price_type'] ?? $price->price_type->value
            )
            ->where('currency_id', $data['currency_id'] ?? $price->currency_id)
            ->where('country_id', $data['country_id'] ?? $price->country_id)
            ->exists();
        if ($hasPriceForCountryCurrency) {
            throw new \Exception('Product already has a price for this type, currency and country');
        }

        if (empty($data['valid_from'])) {
            $data['valid_from'] = now();
        }
        if (!$productPrice->update($data)) {
            throw new \Exception('Error updating product price');
        }

        if (is_array($taxRateIds)) {
            $this->syncTaxRateIds($productPrice, $taxRateIds);
        }
        if (is_array($discountIds)) {
            $this->syncDiscounts($productPrice, $discountIds);
        }

        $priceType = PriceType::tryFrom($data['price_type']);
        if (!$priceType) {
            throw new \Exception('Invalid price type');
        }
        switch ($priceType) {
            case PriceType::ONE_TIME:
                if ($productPrice->subscription()->exists()) {
                    $productPrice->subscription()->delete();
                }
                break;
            case PriceType::SUBSCRIPTION:
                $findSubscription = $productPrice->subscription()->first();
                if (!$findSubscription) {
                    $this->createPriceSubscription($productPrice, $data);
                } else {
                    $this->updatePriceSubscription($findSubscription, $data);
                }
                break;
                // Add more cases as needed
        }
        return true;
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

    public function attachBulkPricesToProduct(Product $product, array $prices)
    {
        $product->prices()->attach($prices);
        return true;
    }

    public function detachBulkPricesFromProduct(Product $product, array $prices)
    {
        $product->prices()->detach($prices);
        return true;
    }
}
