<?php
namespace App\Enums\Order\Discount;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';
    
    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => __('Percentage'),
            self::FIXED => __('Fixed Amount'),
        };
    }
    
}