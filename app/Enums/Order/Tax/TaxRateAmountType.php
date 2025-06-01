<?php
namespace App\Enums\Order\Tax;

enum TaxRateAmountType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => __('Fixed Amount'),
            self::PERCENTAGE => __('Percentage'),
        };
    }
}