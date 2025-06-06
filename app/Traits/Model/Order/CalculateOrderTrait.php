<?php
namespace App\Traits\Model\Order;

trait CalculateOrderTrait
{
    

    /**
     * Calculate the total price of an order.
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $total += $item->calculateTotalPrice();
        }

        return $total;
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
        $totalTax = 0.0;

        foreach ($this->items as $item) {
            $totalTax += $item->calculateTaxWithoutPrice();
        }

        return $totalTax;
    }
    /**
     * Calculate the total discount for an order.
     *
     * @return float
     */
    public function calculateTotalDiscount(): float
    {
        $totalDiscount = 0.0;

        foreach ($this->items as $item) {
            $totalDiscount += $item->calculateDiscount();
        }

        return $totalDiscount;
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

        return ($totalPrice + $totalTax) - $totalDiscount;
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

        return $totalPrice / $totalItems;
    }
    /**
     * Calculate the total shipping cost for an order.
     *
     * @return float
     */
    public function calculateTotalShippingCost(): float
    {
        $totalShippingCost = 0.0;

        foreach ($this->items as $item) {
            $shippingCost = $item->productable->shipping_cost ?? 0.0; // Assuming shipping_cost is a property of the itemable entity
            $totalShippingCost += ($item->quantity * $shippingCost);
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
    /**
     * Calculate the total price after applying any discounts.
     *
     * @return float
     */
    public function calculateTotalPriceAfterDiscounts(): float
    {
        $totalPrice = $this->calculateTotalPrice();
        $totalDiscount = $this->calculateTotalDiscount();

        return $totalPrice - $totalDiscount;
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

        return $totalPrice + $totalTax;
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

        return $totalPrice + $totalTax - $totalDiscount;
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