<?php
namespace App\Enums\Product;


enum ProductType: string
{
    case DIGITAL = 'digital';
    case PHYSICAL = 'physical';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::DIGITAL => 'Digital',
            self::PHYSICAL => 'Physical',
            self::SERVICE => 'Service',
        };
    }
}
