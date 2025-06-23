<?php
namespace App\Enums\Order\Discount;

enum DiscountType: string
{
    case BUY_X_GET_Y = 'buy_x_get_y';
    case FREE_SHIPPING = 'free_shipping';
    case BULK_PURCHASE = 'bulk_purchase';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::BUY_X_GET_Y => __('Buy X Get Y'),
            self::FREE_SHIPPING => __('Free Shipping'),
            self::BULK_PURCHASE => __('Bulk Purchase'),
            self::CUSTOM => __('Custom'),
        };
    }

}
