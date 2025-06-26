<?php
namespace App\Services\Order\Calculate;

use App\Models\Order;
use App\Models\OrderItem;

class OrderCalculateService
{

    public function calculateOrderItemQuantity(OrderItem $orderItem): int
    {
        return $orderItem->quantity;
    }
    /**
     * Calculate the total price of an order item.
     *
     * @param \App\Models\OrderItem $orderItem
     * @return float
     */

    public function calculateOrderItemTotalPrice(OrderItem $orderItem): float
    {
        // Assuming orderItemable has a price property
        return $orderItem->quantity * $orderItem->orderItemable->price;
    }


    public function calculateOrderItemTaxWithoutPrice(OrderItem $orderItem): float
    {
        $taxRate = $orderItem->orderItemable->tax_rate ?? 0.0; // Assuming tax_rate is a property of the itemable entity
        return ($this->calculateOrderItemTotalPrice($orderItem) * ($taxRate / 100));
    }

    /**
     * Calculate the total price of an order item.
     *
     * @param \App\Models\OrderItem $orderItem
     * @return float
     */
    public function calculateOrderItemTotalPriceWithTax(OrderItem $orderItem): float
    {
        $totalPrice = $this->calculateOrderItemTotalPrice($orderItem);
        return $totalPrice + $this->calculateOrderItemTaxWithoutPrice($orderItem);
    }

    public function calculateOrderItemDiscount(OrderItem $orderItem): float
    {
        $discountRate = $orderItem->orderItemable->discount_rate ?? 0.0; // Assuming discount_rate is a property of the itemable entity

        return ($this->calculateOrderItemTotalPrice($orderItem) * ($discountRate / 100));
    }

    /**
     * Calculate the total price of an order item after applying discounts.
     *
     * @param \App\Models\OrderItem $orderItem
     * @return float
     */
    public function calculateOrderItemTotalPriceAfterDiscount(OrderItem $orderItem): float
    {
        $totalPrice = $this->calculateOrderItemTotalPrice($orderItem);
        $discountRate = $orderItem->orderItemable->discount_rate ?? 0.0; // Assuming discount_rate is a property of the itemable entity
        return $totalPrice - ($totalPrice * ($discountRate / 100));
    }

    /**
     * Calculate the total price of an order item after applying tax and discounts.
     *
     * @param \App\Models\OrderItem $orderItem
     * @return float
     */
    public function calculateOrderItemTotalPriceAfterTaxAndDiscount(OrderItem $orderItem): float
    {
        $totalPrice = $this->calculateOrderItemTotalPrice($orderItem);
        $taxRate = $orderItem->orderItemable->tax_rate ?? 0.0; // Assuming tax_rate is a property of the itemable entity
        $discountRate = $orderItem->orderItemable->discount_rate ?? 0.0; // Assuming discount_rate is a property of the itemable entity

        $priceAfterTax = $totalPrice + ($totalPrice * ($taxRate / 100));
        return $priceAfterTax - ($priceAfterTax * ($discountRate / 100));
    }


    /**
     * Calculate the total price of an order.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalPrice(Order $order): float
    {
        $total = 0.0;

        foreach ($order->items as $item) {
            $total += $this->calculateOrderItemTotalPrice($item);
        }

        return $total;
    }

    /**
     * Calculate the total quantity of items in an order.
     *
     * @param \App\Models\Order $order
     * @return int
     */
    public function calculateTotalQuantity(Order $order): int
    {
        $totalQuantity = 0;

        foreach ($order->items as $item) {
            $totalQuantity += $this->calculateOrderItemQuantity($item);
        }

        return $totalQuantity;
    }
    /**
     * Calculate the total tax for an order.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalTax(Order $order): float
    {
        $totalTax = 0.0;

        foreach ($order->items as $item) {
            $totalTax += $this->calculateOrderItemTaxWithoutPrice($item);
        }

        return $totalTax;
    }
    /**
     * Calculate the total discount for an order.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalDiscount(Order $order): float
    {
        $totalDiscount = 0.0;

        foreach ($order->items as $item) {
            $totalDiscount += $this->calculateOrderItemDiscount($item);
        }

        return $totalDiscount;
    }
    /**
     * Calculate the final total price after applying tax and discount.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateFinalTotal(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalTax = $this->calculateTotalTax($order);
        $totalDiscount = $this->calculateTotalDiscount($order);

        return $totalPrice + $totalTax - $totalDiscount;
    }
    /**
     * Calculate the total number of items in an order.
     *
     * @param \App\Models\Order $order
     * @return int
     */
    public function calculateTotalItems(Order $order): int
    {
        $totalItems = 0;

        foreach ($order->items as $item) {
            $totalItems += $item->quantity;
        }

        return $totalItems;
    }
    /**
     * Calculate the average price per item in an order.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateAveragePricePerItem(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalItems = $this->calculateTotalItems($order);

        if ($totalItems === 0) {
            return 0.0; // Avoid division by zero
        }

        return $totalPrice / $totalItems;
    }
    /**
     * Calculate the total shipping cost for an order.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalShippingCost(Order $order): float
    {
        $totalShippingCost = 0.0;

        foreach ($order->items as $item) {
            $shippingCost = $item->orderItemable->shipping_cost ?? 0.0; // Assuming shipping_cost is a property of the itemable entity
            $totalShippingCost += ($item->quantity * $shippingCost);
        }

        return $totalShippingCost;
    }
    /**
     * Calculate the total price including shipping.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalPriceWithShipping(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalShippingCost = $this->calculateTotalShippingCost($order);

        return $totalPrice + $totalShippingCost;
    }
    /**
     * Calculate the total price after applying any discounts.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalPriceAfterDiscounts(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalDiscount = $this->calculateTotalDiscount($order);

        return $totalPrice - $totalDiscount;
    }
    /**
     * Calculate the total price after applying tax.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalPriceAfterTax(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalTax = $this->calculateTotalTax($order);

        return $totalPrice + $totalTax;
    }
    /**
     * Calculate the total price after applying tax and discounts.
     *
     * @param \App\Models\Order $order
     * @return float
     */
    public function calculateTotalPriceAfterTaxAndDiscounts(Order $order): float
    {
        $totalPrice = $this->calculateTotalPrice($order);
        $totalTax = $this->calculateTotalTax($order);
        $totalDiscount = $this->calculateTotalDiscount($order);

        return $totalPrice + $totalTax - $totalDiscount;
    }
    /**
     * Calculate the total price of an order with all calculations applied.
     *
     * @param \App\Models\Order $order
     * @return array
     */
    public function calculateOrderSummary(Order $order): array
    {
        return [
            'total_price' => $this->calculateTotalPrice($order),
            'total_quantity' => $this->calculateTotalQuantity($order),
            'total_tax' => $this->calculateTotalTax($order),
            'total_discount' => $this->calculateTotalDiscount($order),
            'final_total' => $this->calculateFinalTotal($order),
            'total_items' => $this->calculateTotalItems($order),
            'average_price_per_item' => $this->calculateAveragePricePerItem($order),
            'total_shipping_cost' => $this->calculateTotalShippingCost($order),
            'total_price_with_shipping' => $this->calculateTotalPriceWithShipping($order),
            'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts($order),
            'total_price_after_tax' => $this->calculateTotalPriceAfterTax($order),
            'total_price_after_tax_and_discounts' => $this->calculateTotalPriceAfterTaxAndDiscounts($order),
        ];
    }
}
