<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\Discount\DiscountableType;
use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Order\Tax\TaxRateAbleType;
use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Price\PriceType;
use App\Factories\Discount\DiscountableFactory;
use App\Factories\Tax\TaxRateAbleFactory;
use App\Helpers\MathHelpers;
use App\Models\DefaultDiscount;
use App\Models\DefaultTaxRate;
use App\Models\Discount;
use App\Models\Price;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait CalculateOrderItemTrait
{

    private ?PriceType $priceType = null;
    private ?Price $orderItemPrice = null;
    private Collection $defaultTaxRates;
    private Collection $defaultDiscounts;
    private Collection $taxRates;
    private Collection $discounts;

    public function setPriceType(PriceType $priceType): void
    {
        $this->priceType = $priceType;
    }

    public function getPriceType(): ?PriceType
    {
        return $this->priceType;
    }

    public function setOrderItemPrice(Price $orderItemPrice): void
    {
        $this->orderItemPrice = $orderItemPrice;
    }

    public function getOrderItemPrice(): ?Price
    {
        return $this->orderItemPrice;
    }

    public function init(): self
    {
        if ($this->orderItemPrice) {
            return $this;
        }
        if (!$this->order?->currency) {
            throw new \Exception('Order currency is not set');
        }
        $this->orderItemPrice = $this->orderItemable->getPriceByLocaleAndPriceType(
            $this->order->currency,
            $this->priceType
        );
        if (!$this->orderItemPrice) {
            throw new \Exception('Order item price not found for user locale and price type');
        }
        $this->defaultTaxRates = $this->filterValidTaxRates(
            DefaultTaxRate::all()->map(function (DefaultTaxRate $defaultTaxRate) {
                return $defaultTaxRate->taxRate;
            }),
            true
        );
        $this->defaultDiscounts = $this->filterValidDiscounts(
            DefaultDiscount::all()->map(function (DefaultDiscount $defaultDiscount) {
                return $defaultDiscount->discount;
            }),
            true
        );

        if ($this->orderItemPrice->taxRates->isNotEmpty()) {
            $this->taxRates = $this->filterValidTaxRates($this->orderItemPrice->taxRates, false);
        } else {
            $this->taxRates = new Collection();
        }

        if (
            $this->orderItemPrice?->discounts instanceof Collection &&
            $this->orderItemPrice->discounts->isNotEmpty()
        ) {
            $this->discounts = $this->filterValidDiscounts($this->orderItemPrice?->discounts, false);
        } else {
            $this->discounts = new Collection();
        }
        return $this;
    }

    public function filterValidTaxRates(Collection $collection, bool $isDefault): Collection
    {
        return $collection->filter(function (TaxRate $defaultTaxRate) use ($isDefault) {
            return $this->isTaxRateValid($defaultTaxRate, $isDefault);
        });
    }

    public function filterValidDiscounts(Collection $collection, bool $isDefault): Collection
    {
        return $collection->filter(function (Discount $defaultDiscount) use ($isDefault) {
            return $this->isDiscountValid($defaultDiscount, $isDefault);
        });
    }

    private function getTaxByAmountType(TaxRateAmountType $amountType, TaxRate $taxRate): float
    {
        switch ($amountType) {
            case TaxRateAmountType::FIXED:
                if ($taxRate->amount_type !== TaxRateAmountType::FIXED) {
                    return 0.0; // If the tax rate is percentage, return 0 for fixed amount
                }
                return $taxRate->amount ?? 0.0;
            case TaxRateAmountType::PERCENTAGE:
                if ($taxRate->amount_type !== TaxRateAmountType::PERCENTAGE) {
                    return 0.0; // If the tax rate is fixed, return 0 for percentage rate
                }
                return $taxRate->rate ?? 0.0;
            default:
                return 0.0; // No valid amount type
        }
    }

    public function calculateDefaultTaxWithoutPrice(): array
    {
        if ($this->defaultTaxRates->isEmpty()) {
            return [0.0, 0.0]; // No tax rates available
        }
        $totalPercentageRate = 0;
        $totalFixedAmount = 0.0;
        if ($this->defaultTaxRates->isNotEmpty()) {
            foreach ($this->defaultTaxRates as $defaultTaxRate) {
                switch ($defaultTaxRate->amount_type) {
                    case TaxRateType::VAT:
                        $totalPercentageRate += 20; // Example VAT rate
                        break;
                    default:
                        $totalFixedAmount += $this->getTaxByAmountType(
                            TaxRateAmountType::FIXED,
                            $defaultTaxRate
                        );
                        $totalPercentageRate += $this->getTaxByAmountType(
                            TaxRateAmountType::PERCENTAGE,
                            $defaultTaxRate
                        );
                        break;
                }
            }
        }
        return [
            $totalFixedAmount,
            $totalPercentageRate,
        ];
    }
    public function calculateTaxWithoutPrice(float $totalPrice): float
    {
        if ($totalPrice <= 0) {
            return 0.0; // No tax for zero or negative prices
        }

        list($totalFixedAmount, $totalPercentageRate) = $this->calculateDefaultTaxWithoutPrice();

        $priceTaxRates = $this->orderItemPrice->taxRates;

        if ($priceTaxRates->isNotEmpty()) {
            foreach ($this->taxRates as $priceTaxRate) {
                switch ($priceTaxRate->amount_type) {
                    case TaxRateType::VAT:
                        $totalPercentageRate += 20; // Example VAT rate
                        break;
                    default:
                        $totalFixedAmount += $this->getTaxByAmountType(
                            TaxRateAmountType::FIXED,
                            $priceTaxRate
                        );
                        $totalPercentageRate += $this->getTaxByAmountType(
                            TaxRateAmountType::PERCENTAGE,
                            $priceTaxRate
                        );
                        break;
                }
            }
        }
        $calculatePercentage = ($totalPrice * ($totalPercentageRate / 100));
        return MathHelpers::toDecimalPlaces(
            $calculatePercentage + $totalFixedAmount
        );
    }

    /**
     * Calculate the total price of an order item.
     *
     * @return float
     */
    public function calculateTotalPriceWithTax(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        return MathHelpers::toDecimalPlaces(
            $totalPrice + $this->calculateTaxWithoutPrice($totalPrice)
        );
    }

    private function getDiscountByAmountType(DiscountAmountType $amountType, Discount $discount): float
    {
        switch ($amountType) {
            case DiscountAmountType::FIXED:
                if ($discount->amount_type !== DiscountAmountType::FIXED) {
                    return 0.0; // If the discount is percentage, return 0 for fixed amount
                }
                return $discount->amount ?? 0.0;
            case DiscountAmountType::PERCENTAGE:
                if ($discount->amount_type !== DiscountAmountType::PERCENTAGE) {
                    return 0.0; // If the discount is fixed, return 0 for percentage rate
                }
                return $discount->rate ?? 0.0;
            default:
                return 0.0; // No valid amount type
        }
    }
    private function isTaxRateValid(TaxRate $taxRate, bool $isDefault): bool
    {
        if (!$taxRate->isValid()) {
            return false; // Tax rate is not valid
        }

        if ($isDefault && $taxRate->taxRateAbles()->count() === 0) {
            return true; // No taxrateables associated with the discount
        }
        foreach ($taxRate->taxRateAbles as $taxRateAble) {
            $isValid = TaxRateAbleFactory::create(
                TaxRateAbleType::tryFrom($taxRateAble->tax_rateable_type)
            )->isTaxRateValidForOrderItem($taxRateAble, $this);
            if (!$isValid) {
                return false; // Tax rate is not valid for this discountable
            }
        }
        return true; // Discount is valid
    }
    private function isDiscountValid(Discount $discount, bool $isDefault): bool
    {
        if (!$discount->isValid()) {
            return false; // Discount is not valid
        }

        if ($isDefault && $discount->discountables()->count() === 0) {
            return true; // No discountables associated with the discount
        }
        foreach ($discount->discountables as $discountable) {
            $isValid = DiscountableFactory::create(
                DiscountableType::tryFrom($discountable->discountable_type)
            )->isDiscountValidForOrderItem($discountable, $this);
            if (!$isValid) {
                return false; // Discount is not valid for this discountable
            }
        }
        return true; // Discount is valid
    }

    /**
     * Calculate the default discounts for an order item.
     *
     * @return array<DiscountAmountType::FIXED|DiscountAmountType::PERCENTAGE, float>
     */
    public function calculateDefaultDiscounts(): array
    {
        if ($this->defaultDiscounts->isEmpty()) {
            return [
                0.0, // Total fixed amount
                0.0, // Total percentage rate
            ]; // No default discounts available
        }

        $totalPercentageRate = 0;
        $totalFixedAmount = 0.0;
        foreach ($this->defaultDiscounts as $defaultDiscount) {
            switch ($defaultDiscount->type) {
                case DiscountType::BUY_X_GET_Y:
                case DiscountType::FREE_SHIPPING:
                case DiscountType::BULK_PURCHASE:
                    // No discount amount for these types
                    break;
                case DiscountType::CUSTOM:
                default:
                    $totalPercentageRate += $this->getDiscountByAmountType(
                        DiscountAmountType::PERCENTAGE,
                        $defaultDiscount
                    );
                    $totalFixedAmount += $this->getDiscountByAmountType(
                        DiscountAmountType::FIXED,
                        $defaultDiscount
                    );
                    break;
            }
        }

        return [
            $totalFixedAmount,
            $totalPercentageRate,
        ];
    }

    public function calculateDiscount(): float
    {
        list($totalFixedAmount, $totalPercentageRate) = $this->calculateDefaultDiscounts();
        $priceDiscounts = $this->orderItemPrice?->discounts;
        if ($priceDiscounts instanceof Collection && $priceDiscounts->isNotEmpty()) {
            foreach ($this->discounts as $priceDiscount) {
                switch ($priceDiscount->type) {
                    case DiscountType::BUY_X_GET_Y:
                    case DiscountType::FREE_SHIPPING:
                    case DiscountType::BULK_PURCHASE:
                        // No discount amount for these types
                        break;
                    case DiscountType::CUSTOM:
                    default:
                        $totalPercentageRate += $this->getDiscountByAmountType(
                            DiscountAmountType::PERCENTAGE,
                            $priceDiscount
                        );
                        $totalFixedAmount += $this->getDiscountByAmountType(
                            DiscountAmountType::FIXED,
                            $priceDiscount
                        );
                        break;
                }
            }
        }

        return MathHelpers::toDecimalPlaces(
            ($this->calculateTotalPrice() * ($totalPercentageRate / 100)) + $totalFixedAmount
        );
    }


    public function calculateQuantity(): int
    {
        return $this->quantity;
    }

    public function calculateSubscriptionTotalPrice(): float
    {
        $product = $this->orderItemable;
        if (!$product instanceof Product) {
            throw new \Exception('Product not found for order item');
        }
        $price = $product->prices
        ->where('price_type', PriceType::SUBSCRIPTION->value)
        ->first();
        if (!$price) {
            throw new \Exception('Price not found for product');
        }
        $priceSubscription = $price->subscription;

        if (!$priceSubscription) {
            throw new \Exception('Subscription not found for price');
        }
        if (!$priceSubscription->setup_fee_value) {
            return 0.0; // No setup fee, return 0
        }
        return MathHelpers::toDecimalPlaces(
            $priceSubscription->setup_fee_value
        );
    }

    public function calculateOneTimeTotalPrice(): float
    {
        if (!$this->orderItemPrice) {
            return 0.0; // or throw an exception if a default price is required
        }
        return MathHelpers::toDecimalPlaces(
            $this->quantity * $this->orderItemPrice->amount
        );
    }

    /**
     * Calculate the total price of an order item.
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        switch ($this->priceType) {
            case PriceType::SUBSCRIPTION:
                return $this->calculateSubscriptionTotalPrice();
            case PriceType::ONE_TIME:
            default:
                return $this->calculateOneTimeTotalPrice();
        }
    }

    /**
     * Calculate the total price of an order item after applying discounts.
     *
     * @return float
     */
    public function calculateTotalPriceAfterDiscount(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $discount = $this->calculateDiscount();
        return MathHelpers::toDecimalPlaces(
            $totalPrice - $discount
        );
    }

    /**
     * Calculate the total price of an order item after applying tax and discounts.
     *
     * @return float
     */
    public function calculateTotalPriceAfterTaxAndDiscount(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $discount = $this->calculateDiscount();
        $calculateTax = $this->calculateTaxWithoutPrice($totalPrice);

        return MathHelpers::toDecimalPlaces(
            ($totalPrice + $calculateTax) - $discount
        );
    }

    public function getDefaultDiscounts(): Collection
    {
        return $this->defaultDiscounts;
    }

    public function getDefaultTaxRates(): Collection
    {
        return $this->defaultTaxRates;
    }

    public function getTaxRates(): Collection
    {
        return $this->taxRates;
    }

    public function getDiscounts(): Collection
    {
        return $this->discounts;
    }
}
