<?php
namespace App\Enums\Product;

use App\Models\Product;

enum ProductType: string
{
    case PRODUCT = 'product';

    public function id(): string
    {
        return match ($this) {
            self::PRODUCT => 'product',
        };
    }
}
