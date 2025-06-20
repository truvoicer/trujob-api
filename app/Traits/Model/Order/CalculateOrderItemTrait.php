<?php

namespace App\Traits\Model\Order;

use App\Enums\Order\Discount\DiscountType;
use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Price\PriceType;
use App\Models\DefaultTaxRate;
use App\Models\Price;
use Illuminate\Database\Eloquent\Collection;

trait CalculateOrderItemTrait
{

    private ?PriceType $priceType = null;
    private ?Price $defaultPrice = null;
    private ?Collection $defaultTaxRates = null;
    private ?Collection $defaultDiscounts = null;

    public function setPriceType(PriceType $priceType): void
    {
        $this->priceType = $priceType;
    }

    public function getPriceType(): ?PriceType
    {
        return $this->priceType;
    }

    public function setDefaultPrice(Price $defaultPrice): void
    {
        $this->defaultPrice = $defaultPrice;
    }

    public function getDefaultPrice(): Price
    {
        return $this->defaultPrice;
    }

    public function init(): self
    {
        if ($this->defaultPrice) {
            return $this;
        }
        $this->defaultPrice = $this->productable->getDefaultPrice($this->priceType);
        $this->defaultTaxRates = DefaultTaxRate::all();
        return $this;
    }

    public function calculateQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Calculate the total price of an order item.
     *
     * @return float
     */

    public function calculateTotalPrice(): float
    {
        $this->init();
        if (!$this->defaultPrice) {
            return 0.0; // or throw an exception if a default price is required
        }
        return $this->quantity * $this->defaultPrice->amount;
    }

    public function calculateTaxWithoutPrice(float $totalPrice): float
    {
        if ($totalPrice <= 0) {
            return 0.0; // No tax for zero or negative prices
        }
        $this->init();
        $priceTaxRates = $this->defaultPrice->taxRates;

        $totalPercentageRate = 0;
        $totalFixedAmount = 0.0;
        if ($this->defaultTaxRates->isNotEmpty()) {
            foreach ($this->defaultTaxRates as $defaultTaxRate) {
                switch ($defaultTaxRate->amount_type) {
                    case TaxRateAmountType::FIXED:
                        $totalFixedAmount += $defaultTaxRate->taxRate->amount ?? 0.0;
                        break;
                    case TaxRateAmountType::PERCENTAGE:
                        $totalPercentageRate += $defaultTaxRate->taxRate->rate ?? 0;
                        break;
                }
            }
        }
        if ($priceTaxRates->isNotEmpty()) {
            foreach ($priceTaxRates as $priceTaxRate) {
                switch ($priceTaxRate->amount_type) {
                    case TaxRateAmountType::FIXED:
                        $totalFixedAmount += $priceTaxRate->taxRate->amount ?? 0.0;
                        break;
                    case TaxRateAmountType::PERCENTAGE:
                        $totalPercentageRate += $priceTaxRate->taxRate->rate ?? 0;
                        break;
                }
            }
        }
        $calculatePercentage = ($totalPrice * ($totalPercentageRate / 100));
        return $calculatePercentage + $totalFixedAmount;
    }

    /**
     * Calculate the total price of an order item.
     *
     * @return float
     */
    public function calculateTotalPriceWithTax(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        return $totalPrice + $this->calculateTaxWithoutPrice($totalPrice);
    }

    public function calculateDiscount(): float
    {

        $this->init();
        $priceDiscounts = $this->defaultPrice?->discounts;
        if ($priceDiscounts === null) {
            return 0.0; // No discounts available
        }
        $totalPercentageRate = 0;
        $totalFixedAmount = 0.0;
        if ($this->defaultDiscounts->isNotEmpty()) {
            foreach ($this->defaultDiscounts as $defaultDiscount) {
                switch ($defaultDiscount->amount_type) {
                    case DiscountType::FIXED:
                        $totalFixedAmount += $defaultDiscount->discount->amount ?? 0.0;
                        break;
                    case DiscountType::PERCENTAGE:
                        $totalPercentageRate += $defaultDiscount->discount->rate ?? 0;
                        break;
                }
            }
        }
        if ($priceDiscounts->isNotEmpty()) {
            foreach ($priceDiscounts as $priceDiscount) {
                switch ($priceDiscount->amount_type) {
                    case DiscountType::FIXED:
                        $totalFixedAmount += $priceDiscount->amount ?? 0.0;
                        break;
                    case DiscountType::PERCENTAGE:
                        $totalPercentageRate += $priceDiscount->rate ?? 0;
                        break;
                }
            }
        }

        return ($this->calculateTotalPrice() * ($totalPercentageRate / 100)) + $totalFixedAmount;
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
        return $totalPrice - $discount;
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

        return ($totalPrice + $calculateTax) - $discount;
    }
}
