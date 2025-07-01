<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\OrderItemable;
use App\Enums\Order\OrderItemType;
use App\Enums\Product\ProductWeightUnit;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait CalculateOrderShippingTrait
{

    private Collection $shippingMethods;


    /**
     * Calculate the total shipping cost for an order.
     *
     * @return float
     */
    public function calculateTotalShippingCost(): float
    {
        $this->shippingMethods = new Collection();
        $this->findShippingMethodTier();
        dd($this->shippingMethods);
        $totalShippingCost = 0.0;

        foreach ($this->items as $item) {

            // $totalShippingCost += ($item->quantity * $shippingCost);
        }

        return $totalShippingCost;
    }

    /**
     * Calculate the total price including shipping.
     *
     * @return float
     */
    public function calculateTotalPriceWithShipping(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalShippingCost = $this->calculateTotalShippingCost();

        return $totalPrice + $totalShippingCost;
    }

    public function getProductWeightInGrams(Product $product): float
    {
        switch ($product->weight_unit) {
            case ProductWeightUnit::G:
                return $product->weight;
            case ProductWeightUnit::KG:
                return $product->weight * 1000; // Convert kilograms to grams
            case ProductWeightUnit::LB:
                return $product->weight * 0.453592 * 1000; // Convert pounds to grams
                // case ProductWeightUnit::OZ:
                //     return $product->weight * 0.0283495 * 1000; // Convert ounces to grams
            default:
                return 0.0; // Default case if no weight unit is set
        }
    }

    public function calculateTotalWeight(): float
    {
        $totalWeight = 0.0;

        foreach ($this->items as $item) {
            switch ($item->order_itemable_type) {
                case OrderItemType::PRODUCT:
                    $weight = $this->getProductWeightInGrams($item->orderItemable);
                    $totalWeight += ($item->quantity * $weight);
                    break;
            }
        }

        return $totalWeight;
    }

    public function initializeShippingMethodProducts(
        Collection|EloquentCollection|MorphToMany $shippingMethods,
        Product $product
    ): void {
        $shippingMethods->each(function ($shippingMethod) use ($product) {
            $findShippingMethod = $this->shippingMethods->where('shipping_method.id', $shippingMethod->id)->first();

            if (!$findShippingMethod) {
                $this->shippingMethods->add([
                    'shipping_method' => $shippingMethod,
                    'products' => (new Collection()),
                ]);
                $findShippingMethod = $this->shippingMethods->where('shipping_method.id', $shippingMethod->id)->first();
            }

            $findShippingMethod['products'][] = $product;
        });
    }

    public function findShippingMethodTier()
    {
        foreach ($this->items as $item) {
            switch ($item->order_itemable_type) {
                case OrderItemable::PRODUCT:
                    $product = $item->orderItemable;
                    if (!$product) {
                        continue; // Skip if the product is not found
                    }
                    if (!$product->shippingMethods()->count()) {
                        $this->initializeShippingMethodProducts(ShippingMethod::all(), $product);
                        continue;
                    }
                    $this->initializeShippingMethodProducts($product->shippingMethods(), $product);
                    break;
            }
        }
    }
}
