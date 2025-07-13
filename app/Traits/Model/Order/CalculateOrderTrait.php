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
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait CalculateOrderTrait
{
    use CalculateOrderShippingTrait;

    private ?PriceType $priceType = null;
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

    public function init(): self
    {
        $this->defaultTaxRates = $this->filterValidTaxRates(DefaultTaxRate::all(), true);
        $this->defaultDiscounts = $this->filterValidDiscounts(DefaultDiscount::all(), true);

        return $this;
    }

    public function filterValidTaxRates(Collection $collection, bool $isDefault): Collection
    {
        return $collection->filter(function (DefaultTaxRate $defaultTaxRate) use ($isDefault) {
            return $this->isTaxRateValid($defaultTaxRate->taxRate, $isDefault);
        })->map(function (DefaultTaxRate $defaultTaxRate) {
            return $defaultTaxRate->taxRate;
        });
    }

    public function filterValidDiscounts(Collection $collection, bool $isDefault): Collection
    {
        return $collection->filter(function (DefaultDiscount $defaultDiscount) use ($isDefault) {
            return $this->isDiscountValid($defaultDiscount->discount, $isDefault);
        })->map(function (DefaultDiscount $defaultDiscount) {
            return $defaultDiscount->discount;
        });
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
            )->isTaxRateValidForOrder($taxRateAble, $this);
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
            )->isDiscountValidForOrder($discountable, $this);
            if (!$isValid) {
                return false; // Discount is not valid for this discountable
            }
        }
        return true; // Discount is valid
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
    /**
     * Calculate the total price of an order.
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $item->setPriceType($this->getPriceType());
            $item->init(); // Ensure the item is initialized with the correct price type
            $total += $item->calculateTotalPrice();
        }

        return MathHelpers::toDecimalPlaces(
            $total
        );
    }

    /**
     * Calculate the total quantity of items in an order.
     *
     * @return int
     */
    public function calculateTotalQuantity(): int
    {
        $totalQuantity = 0;

        foreach ($this->items as $item) {
            $item->setPriceType($this->getPriceType());
            $item->init(); // Ensure the item is initialized with the correct price type
            $totalQuantity += $item->calculateQuantity();
        }

        return $totalQuantity;
    }

    /**
     * Calculate the total tax for an order.
     *
     * @return float
     */
    public function calculateTotalTax(): float
    {
        list($totalFixedAmount, $totalPercentageRate) = $this->calculateDefaultTaxWithoutPrice();
        return MathHelpers::toDecimalPlaces(
            ($this->calculateTotalPrice() * ($totalPercentageRate / 100)) + $totalFixedAmount
        );
    }

    /**
     * Calculate the total discount for an order.
     *
     * @return float
     */
    public function calculateTotalDiscount(): float
    {

        list($totalFixedAmount, $totalPercentageRate) = $this->calculateDefaultDiscounts();


        return MathHelpers::toDecimalPlaces(
            ($this->calculateTotalPrice() * ($totalPercentageRate / 100)) + $totalFixedAmount
        );
    }
    /**
     * Calculate the final total price after applying tax and discount.
     *
     * @return float
     */
    public function calculateFinalTotal(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalTax = $this->calculateTotalTax();
        $totalDiscount = $this->calculateTotalDiscount();

        return MathHelpers::toDecimalPlaces(
            ($totalPrice + $totalTax) - $totalDiscount
        );
    }
    /**
     * Calculate the total number of items in an order.
     *
     * @return int
     */
    public function calculateTotalItems(): int
    {
        $totalItems = 0;

        foreach ($this->items as $item) {
            $totalItems += $item->quantity;
        }

        return $totalItems;
    }
    /**
     * Calculate the average price per item in an order.
     *
     * @return float
     */
    public function calculateAveragePricePerItem(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalItems = $this->calculateTotalItems();

        if ($totalItems === 0) {
            return 0.0; // Avoid division by zero
        }

        return MathHelpers::toDecimalPlaces(
            $totalPrice / $totalItems
        );
    }

    /**
     * Calculate the total price after applying any discounts.
     *
     * @return float
     */
    public function calculateTotalPriceAfterDiscounts(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalDiscount = $this->calculateTotalDiscount();

        return MathHelpers::toDecimalPlaces(
            $totalPrice - $totalDiscount
        );
    }
    /**
     * Calculate the total price after applying tax.
     *
     * @return float
     */
    public function calculateTotalPriceAfterTax(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalTax = $this->calculateTotalTax();

        return MathHelpers::toDecimalPlaces(
            $totalPrice + $totalTax
        );
    }
    /**
     * Calculate the total price after applying tax and discounts.
     *
     * @return float
     */
    public function calculateTotalPriceAfterTaxAndDiscounts(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalTax = $this->calculateTotalTax();
        $totalDiscount = $this->calculateTotalDiscount();

        return MathHelpers::toDecimalPlaces(
            $totalPrice + $totalTax - $totalDiscount
        );
    }

    /**
     * Calculate the total price of an order with all calculations applied.
     *
     * @return array
     */
    public function calculateOrderSummary(): array
    {
        return [
            'total_price' => $this->calculateTotalPrice(),
            'total_quantity' => $this->calculateTotalQuantity(),
            'total_tax' => $this->calculateTotalTax(),
            'total_discount' => $this->calculateTotalDiscount(),
            'final_total' => $this->calculateFinalTotal(),
            'total_items' => $this->calculateTotalItems(),
            'average_price_per_item' => $this->calculateAveragePricePerItem(),
            'total_shipping_cost' => $this->calculateTotalShippingCost(),
            'total_price_with_shipping' => $this->calculateTotalPriceWithShipping(),
            'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts(),
            'total_price_after_tax' => $this->calculateTotalPriceAfterTax(),
            'total_price_after_tax_and_discounts' => $this->calculateTotalPriceAfterTaxAndDiscounts(),
        ];
    }
}


