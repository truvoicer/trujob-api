<?php
namespace App\Enums\Order\Tax;

enum TaxScope : string
{
    case PRODUCT = 'product';
    case SHIPPING = 'shipping';
    case ORDER = 'order';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => __('Product'),
            self::SHIPPING => __('Shipping'),
            self::ORDER => __('Order'),
        };
    }
}