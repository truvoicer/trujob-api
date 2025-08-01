<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\OrderItemable;
use App\Enums\Order\Shipping\ShippingRateType;
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
            $totalDimensionalWeight = $shippingMethodData[ShippingRateType::DIMENSION_BASED->value] ?? 0.0;
            $totalWeight = $shippingMethodData[ShippingRateType::WEIGHT_BASED->value] ?? 0.0;
            $hasFlatRateShipping = $shippingMethodData[ShippingRateType::FLAT_RATE->value] ?? false;
            $hasFreeShipping = $shippingMethodData[ShippingRateType::FREE->value] ?? false;
            $productIds = $shippingMethodData['product_ids'] ?? [];

            if ($shippingMethodId === array_key_first($data)) {
                $shippingMethodQuery->where(function ($query) use (
                    $shippingMethodId,
                    $totalDimensionalWeight,
                    $totalWeight,
                    $hasFlatRateShipping,
                    $hasFreeShipping,
                    $productIds
                ) {
                    $query = $this->whereQuery(
                        $query,
                        $shippingMethodId,
                        $totalDimensionalWeight,
                        $totalWeight,
                        $hasFlatRateShipping,
                        $hasFreeShipping,
                        $productIds
                    );
                });
                continue;
            }

            $shippingMethodQuery->orWhere(function ($query) use (
                $shippingMethodId,
                $totalDimensionalWeight,
                $totalWeight,
                $hasFlatRateShipping,
                $hasFreeShipping,
                $productIds
            ) {
                $query = $this->whereQuery(
                    $query,
                    $shippingMethodId,
                    $totalDimensionalWeight,
                    $totalWeight,
                    $hasFlatRateShipping,
                    $hasFreeShipping,
                    $productIds
                );
            })
                ->with([
                    'rates' => function ($query) use (
                        $totalDimensionalWeight,
                        $totalWeight,
                        $hasFlatRateShipping,
                        $hasFreeShipping,
                        $productIds
                    ) {
                        $query = $this->ratesQuery(
                            $query,
                            $totalDimensionalWeight,
                            $totalWeight,
                            $hasFlatRateShipping,
                            $hasFreeShipping,
                            $productIds
                        );
                        // $query->orderByRaw("{$this->calculateDimensionalWeightDivisorCase()} ASC");
                    },
                ]);
        }

        if (isset($this->shippingMethod) && $this->shippingMethod instanceof ShippingMethod) {
            $shippingMethodQuery->where('id', $this->shippingMethod->id);
        }

        return $shippingMethodQuery
            ->get()
            ->transform(
                function ($shippingMethod) use ($data) {
                    $shippingMethod->products = Product::whereIn(
                        'id',
                        $data[$shippingMethod->id]['product_ids'] ?? []
                    )->get();
                    return $shippingMethod;
                }
            );
    }

    private function whereQuery(
        $query,
        int $shippingMethodId,
        int $totalDimensionalWeight,
        int $totalWeight,
        bool $hasFlatRateShipping = false,
        bool $hasFreeShipping = false,
        array $productIds = []
    ) {
        $query->where('id', $shippingMethodId)
            ->whereHas('rates', function ($query) use (
                $totalDimensionalWeight,
                $totalWeight,
                $hasFlatRateShipping,
                $hasFreeShipping,
                $productIds
            ) {
                $query = $this->ratesQuery(
                    $query,
                    $totalDimensionalWeight,
                    $totalWeight,
                    $hasFlatRateShipping,
                    $hasFreeShipping,
                    $productIds
                );
            });
        return $query;
    }

    private function ratesQuery(
        $query,
        int $totalDimensionalWeight,
        int $totalWeight,
        bool $hasFlatRateShipping = false,
        bool $hasFreeShipping = false,
        array $productIds = []
    ) {

        // case FLAT_RATE = 'flat_rate';
        // case FREE = 'free';
        // case WEIGHT_BASED = 'weight_based';
        // case PRICE_BASED = 'price_based';
        // case DIMENSION_BASED = 'dimension_based';
        // case CUSTOM = 'custom';

        $query->where(function ($query) use ($totalDimensionalWeight) {
            $query->where('type', ShippingRateType::DIMENSION_BASED->value)
                ->whereRaw(
                    "{$this->calculateDimensionalWeightDivisorCase()} >= ?",
                    [$totalDimensionalWeight]
                );
        })
            ->orWhere(function ($query) use ($totalWeight) {
                $query->where('type', ShippingRateType::WEIGHT_BASED->value)
                    ->whereRaw(
                        "{$this->calculateWeightCase()} >= ?",
                        [$totalWeight]
                    );
            });
        if ($hasFlatRateShipping) {
            $query->orWhere('type', ShippingRateType::FLAT_RATE->value);
        }
        if ($hasFreeShipping) {
            $query->orWhere('type', ShippingRateType::FREE->value);
        }
        return $query;
    }

    private function calculateDimensionalWeightDivisorCase()
    {
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
            CASE width_unit
                WHEN 'M' THEN max_width
                WHEN 'CM' THEN max_width / 100
                WHEN 'MM' THEN max_width / 1000
                WHEN 'IN' THEN max_width * 0.0254
                WHEN 'FT' THEN max_width * 0.3048
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
            CASE depth_unit
                WHEN 'M' THEN max_depth
                WHEN 'CM' THEN max_depth / 100
                WHEN 'MM' THEN max_depth / 1000
                WHEN 'IN' THEN max_depth * 0.0254
                WHEN 'FT' THEN max_depth * 0.3048
                ELSE 0.0
            END
        ) / dimensional_weight_divisor
    ";
    }

    private function calculateWeightCase()
    {
        return "
        (
            CASE weight_unit
                WHEN 'G' THEN max_weight
                WHEN 'KG' THEN max_weight * 1000
                WHEN 'LB' THEN max_weight * 0.453592 * 1000
                -- WHEN 'OZ' THEN max_weight * 0.0283495 * 1000
                ELSE 0.0
            END
        )
    ";
    }

    /**
     * Calculate the total shipping cost for an order.
     *
     * @return float
     */
    public function calculateTotalShippingCost(): float
    {
        $this->initializeShippingMethodProducts();

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
        if ($product->has_depth) {
            $length = $this->getProductDimensionInCmByUnit(
                $product->depth_unit,
                $product->depth ?? 0.0
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
                        break;
                    }
                    if ($product->shippingMethods->isNotEmpty()) {
                        $shippingMethods = $product->shippingMethods;
                    } else {
                        $shippingMethods = ShippingMethod::all();
                    }
                    foreach ($shippingMethods as $shippingMethod) {
                        if (!isset($data[$shippingMethod->id])) {
                            $itemsCollection = new Collection();
                            $itemsCollection->add($item);
                            $data[$shippingMethod->id] = [
                                'product_ids' => [$product->id],
                                'items' => $itemsCollection,
                            ];
                        }
                        if (!in_array($product->id, $data[$shippingMethod->id]['product_ids'])) {
                            $data[$shippingMethod->id]['product_ids'][] = $product->id;
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
                        return $carry + ($this->getDimensionalWeightForProduct($product));
                }
                return $carry;
            });
            $totalWeight = $shippingMethodData['items']->reduce(function ($carry, $item) {
                switch ($item->order_itemable_type) {
                    case OrderItemable::PRODUCT:
                        $product = $item->orderItemable;

                        return $carry + ($this->getProductWeightInGrams($product));
                }
                return $carry;
            });
            $hasFreeShipping = $shippingMethodData['items']->contains(function ($item) {
                return $item->orderItemable->shippingMethods->contains(function ($shippingMethod) {
                    return $shippingMethod->type === ShippingRateType::FREE;
                });
            });
            $hasFlatRateShipping = $shippingMethodData['items']->contains(function ($item) {
                return $item->orderItemable->shippingMethods->contains(function ($shippingMethod) {
                    return $shippingMethod->type === ShippingRateType::FLAT_RATE;
                });
            });
            $data[$shippingMethodId][ShippingRateType::WEIGHT_BASED->value] = $totalWeight;
            $data[$shippingMethodId][ShippingRateType::DIMENSION_BASED->value] = $totalDimensionalWeight;
            $data[$shippingMethodId][ShippingRateType::FREE->value] = $hasFreeShipping;
            $data[$shippingMethodId][ShippingRateType::FLAT_RATE->value] = $hasFlatRateShipping;
            unset($data[$shippingMethodId]['items']);
        }
        return $data;
    }
}
