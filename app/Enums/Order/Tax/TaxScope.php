<?php
namespace App\Enums\Order\Tax;

enum TaxScope : string
{
    case PRODUCT = 'product';
    case SHIPPING = 'shipping';
    case ALL = 'all';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => __('Product'),
            self::SHIPPING => __('Shipping'),
            self::ALL => __('All'),
        };
    }
}