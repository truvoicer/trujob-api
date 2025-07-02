<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\OrderItemable;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait CalculateOrderShippingTrait
{

    private Collection $shippingMethodProducts;
    private ?ShippingMethod $shippingMethod = null;

    public function setShippingMethod(?ShippingMethod $shippingMethod = null): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function getShippingMethod(): ?ShippingMethod
    {
        return $this->shippingMethod;
    }

    public function availableShippingMethods(): Collection
    {
        $this->initializeShippingMethodProducts();

        $this->shippingMethodProducts = $this->shippingMethodProducts->filter(function ($shippingMethodProduct) {
            return $shippingMethodProduct['items']->isNotEmpty();
        })->map(function ($shippingMethodProduct) {
            return $shippingMethodProduct['shipping_method'];
        });
        return $this->shippingMethodProducts;
    }

    /**
     * Calculate the total shipping cost for an order.
     *
     * @return float
     */
    public function calculateTotalShippingCost(): float
    {
        $this->initializeShippingMethodProducts();
        $this->processShippingMethodProductCalculations();
        dd($this->shippingMethodProducts);
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

    public function getProductDimensionInCmByUnit(ProductUnit $productUnit, float $value): float
    {
        switch ($productUnit) {
            case ProductUnit::CM:
                return $value; // Already in centimeters
            case ProductUnit::FEET:
                return $value * 30.48;
            case ProductUnit::INCH:
                return $value * 2.54;
            default:
                return 0.0; // Default case if no weight unit is set
        }
    }

    public function getShippingDimensionInCmByUnit(ShippingUnit $shippingUnit, float $value): float
    {
        switch ($shippingUnit) {
            case ShippingUnit::CM:
                return $value; // Already in centimeters
            case ShippingUnit::FEET:
                return $value * 30.48;
            case ShippingUnit::INCH:
                return $value * 2.54;
            default:
                return 0.0; // Default case if no weight unit is set
        }
    }


    public function getDimensionalWeightForProduct(Product $product): float
    {
        $height = 0.0;
        $length = 0.0;
        $width = 0.0;
        if ($product->has_width) {
            $width = $this->getProductDimensionInCmByUnit(
                $product->width_unit,
                $product->width ?? 0.0
            );
        }
        if ($product->has_height) {
            $height = $this->getProductDimensionInCmByUnit(
                $product->height_unit,
                $product->height ?? 0.0
            );
        }
        if ($product->has_length) {
            $length = $this->getProductDimensionInCmByUnit(
                $product->length_unit,
                $product->length ?? 0.0
            );
        }
        var_dump("Width: $width");
        var_dump("Height: $height");
        var_dump("Length: $length");
        var_dump("Dimensional Weight: " . ($width * $height * $length) / 50);
        return ($width * $height * $length) / 50;
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
                case OrderItemable::PRODUCT:
                    $weight = $this->getProductWeightInGrams($item->orderItemable);
                    $totalWeight += ($item->quantity * $weight);
                    break;
            }
        }

        return $totalWeight;
    }

    public function processShippingMethodProductCalculations(): void
    {
        $this->shippingMethodProducts = $this->shippingMethodProducts->map(function ($shippingMethodProduct, $index) {
            $items = $shippingMethodProduct['items'];

            $weight = $items->reduce(function ($carry, $item) {
                switch ($item->order_itemable_type) {
                    case OrderItemable::PRODUCT:
                        $productWeight = $this->getProductWeightInGrams($item->orderItemable);
                        return $carry + ($productWeight * $item->quantity);
                }
            });

            $totalDimensionalWeight = $items->reduce(function ($carry, $item) {
                switch ($item->order_itemable_type) {
                    case OrderItemable::PRODUCT:
                        $product = $item->orderItemable;
                        return $carry + ($this->getDimensionalWeightForProduct($product) * $item->quantity);
                }
                return $carry;
            });

            $shippingMethod = $shippingMethodProduct['shipping_method'];

            $shippingMethod->tiers()->each(function ($tier) {

                $width = 0.0;
                $height = 0.0;
                $length = 0.0;
                if ($tier->has_width) {
                    $width = $this->getShippingDimensionInCmByUnit(
                        $tier->width_unit,
                        $tier->max_width ?? 0.0
                    );
                }
                if ($tier->has_height) {
                    $height = $this->getShippingDimensionInCmByUnit(
                        $tier->height_unit,
                        $tier->max_height ?? 0.0
                    );
                }
                if ($tier->has_length) {
                    $length = $this->getShippingDimensionInCmByUnit(
                        $tier->length_unit,
                        $tier->max_length ?? 0.0
                    );
                }
                $total = ($width * $height * $length) / 50;

                var_dump("Width: $width");
                var_dump("Height: $height");
                var_dump("Length: $length");
                var_dump("Dimensional Weight: " . ($width * $height * $length) / 50);
                dd($total);
            });
            $shippingMethodProduct['total_weight'] = $weight;
            $shippingMethodProduct['dimensional_weight'] = $totalDimensionalWeight;

            return $shippingMethodProduct;
        });
    }

    public function processShippingMethodProduct(ShippingMethod $shippingMethod, OrderItem $item): void
    {
        $findShippingMethod = $this->shippingMethodProducts->where('shipping_method.id', $shippingMethod->id)->first();

        if (!$findShippingMethod) {
            $this->shippingMethodProducts->add([
                'shipping_method' => $shippingMethod,
                'items' => (new Collection()),
                'total_weight' => 0.0,
                'total_cost' => 0.0,
                'total_quantity' => 0,
                'dimensional_weight' => 0.0,
                'shipping_method_dimensional_weight' => 0.0,
            ]);
            $findShippingMethod = $this->shippingMethodProducts->where('shipping_method.id', $shippingMethod->id)->first();
        }

        $findShippingMethod['items'][] = $item;
    }

    public function shippingMethodProductIterator(
        Collection|EloquentCollection|MorphToMany $shippingMethodProducts,
        OrderItem $item
    ): void {
        $shippingMethodProducts->each(function ($shippingMethod) use ($item) {
            $this->processShippingMethodProduct($shippingMethod, $item);
        });
    }

    public function initializeShippingMethodProducts()
    {
        $this->shippingMethodProducts = new Collection();
        foreach ($this->items as $item) {
            switch ($item->order_itemable_type) {
                case OrderItemable::PRODUCT:
                    $product = $item->orderItemable;
                    if (!$product) {
                        continue;
                    }
                    if (!$product->shippingMethods()->count()) {
                        $this->shippingMethodProductIterator(ShippingMethod::all(), $item);
                        continue;
                    }
                    $this->shippingMethodProductIterator($product->shippingMethods(), $item);
                    break;
            }
        }
    }
}
