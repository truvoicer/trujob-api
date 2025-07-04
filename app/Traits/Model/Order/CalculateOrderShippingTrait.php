<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\OrderItemable;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

trait CalculateOrderShippingTrait
{

    private Collection $availableShippingMethods;
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

    public function setAvailableShippingMethods(Collection $availableShippingMethods): void
    {
        $this->availableShippingMethods = $availableShippingMethods;
    }

    public function getAvailableShippingMethods(): Collection
    {
        return $this->availableShippingMethods ?? new Collection();
    }

    public function availableShippingMethods(): Collection
    {
        $data = $this->initializeShippingMethodProducts();
        $shippingMethodIds = [];

        foreach ($this->items as $item) {
            $shippingMethodIds = array_merge(
                $shippingMethodIds,
                $item->orderItemable->shippingMethods()->get()->pluck('id')->toArray()
            );
        }

        $shippingMethodQuery = ShippingMethod::query();

        foreach ($data as $shippingMethodId => $shippingMethodData) {
            $totalDimensionalWeight = $shippingMethodData['total_dimensional_weight'] ?? 0.0;
            if ($shippingMethodId === array_key_first($data)) {
                $shippingMethodQuery->where(function ($query) use ($shippingMethodId, $totalDimensionalWeight) {
                    $query = $this->whereQuery($query, $shippingMethodId, $totalDimensionalWeight);
                });
                continue;
            }

            $shippingMethodQuery->orWhere(function ($query) use ($shippingMethodId, $totalDimensionalWeight) {
                $query = $this->whereQuery($query, $shippingMethodId, $totalDimensionalWeight);
            })
                ->with(['tiers' => function ($query) use ($totalDimensionalWeight) {
                    $query = $this->tiersQuery($query, $totalDimensionalWeight);
                    $query->orderByRaw("{$this->calculateDimensionalWeightDivisorCase()} ASC");
                }]);
        }

        if (isset($this->shippingMethod) && $this->shippingMethod instanceof ShippingMethod) {
            $shippingMethodQuery->where('id', $this->shippingMethod->id);
        }

        return $shippingMethodQuery->get();
    }

    private function whereQuery($query, int $shippingMethodId, int $totalDimensionalWeight)
    {
        $query->where('id', $shippingMethodId)
            ->whereHas('tiers', function ($query) use ($totalDimensionalWeight) {
                $query = $this->tiersQuery($query, $totalDimensionalWeight);
            });
        return $query;
    }

    private function calculateDimensionalWeightDivisorCase() {
        return "
        (
            CASE weight_unit
                WHEN 'G' THEN max_weight
                WHEN 'KG' THEN max_weight * 1000
                WHEN 'LB' THEN max_weight * 0.453592 * 1000
                -- WHEN 'OZ' THEN max_weight * 0.0283495 * 1000
                ELSE 0.0
            END
            +
            CASE height_unit
                WHEN 'M' THEN max_height
                WHEN 'CM' THEN max_height / 100
                WHEN 'MM' THEN max_height / 1000
                WHEN 'IN' THEN max_height * 0.0254
                WHEN 'FT' THEN max_height * 0.3048
                ELSE 0.0
            END
            +
            CASE length_unit
                WHEN 'M' THEN max_length
                WHEN 'CM' THEN max_length / 100
                WHEN 'MM' THEN max_length / 1000
                WHEN 'IN' THEN max_length * 0.0254
                WHEN 'FT' THEN max_length * 0.3048
                ELSE 0.0
            END
        ) / dimensional_weight_divisor
    ";
    }

    private function tiersQuery($query, int $totalDimensionalWeight)
    {
        $query->whereRaw(
            "{$this->calculateDimensionalWeightDivisorCase()} >= ?",
            [$totalDimensionalWeight]
        );
        return $query;
    }

    /**
     * Calculate the total shipping cost for an order.
     *
     * @return float
     */
    public function calculateTotalShippingCost(): float
    {
        $this->initializeShippingMethodProducts();();
        $this->processShippingMethodProductCalculations
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

    public function initializeShippingMethodProducts()
    {
        $data = [];
        foreach ($this->items as $item) {
            switch ($item->order_itemable_type) {
                case OrderItemable::PRODUCT:
                    $product = $item->orderItemable;
                    if (!$product) {
                        continue;
                    }
                    foreach ($product->shippingMethods as $shippingMethod) {
                        if (!isset($data[$shippingMethod->id])) {
                            $itemsCollection = new Collection();
                            $itemsCollection->add($item);
                            $data[$shippingMethod->id] = [
                                'items' => $itemsCollection,
                            ];
                        }
                        $data[$shippingMethod->id]['items']->add($item);
                    }
                    break;
            }
        }
        foreach ($data as $shippingMethodId => $shippingMethodData) {
            $totalDimensionalWeight = $shippingMethodData['items']->reduce(function ($carry, $item) {
                switch ($item->order_itemable_type) {
                    case OrderItemable::PRODUCT:
                        $product = $item->orderItemable;
                        return $carry + ($this->getDimensionalWeightForProduct($product) * $item->quantity);
                }
                return $carry;
            });
            $data[$shippingMethodId]['total_dimensional_weight'] = $totalDimensionalWeight;
            unset($data[$shippingMethodId]['items']);
        }

        return $data;
    }
}
