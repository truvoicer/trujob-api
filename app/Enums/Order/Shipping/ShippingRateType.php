<?php
namespace App\Enums\Order\Shipping;

enum ShippingRateType: string
{
    case FLAT_RATE = 'flat_rate';
    case FREE = 'free';
    case WEIGHT_BASED = 'weight_based';
    case PRICE_BASED = 'price_based';
    case DIMENSION_BASED = 'dimension_based';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::FLAT_RATE => 'Flat Rate',
            self::FREE => 'Free Shipping',
            self::WEIGHT_BASED => 'Weight Based',
            self::PRICE_BASED => 'Price Based',
            self::DIMENSION_BASED => 'Dimension Based',
            self::CUSTOM => 'Custom Rate',
        };
    }
}