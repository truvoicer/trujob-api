<?php
namespace App\Enums\Product;

use App\Models\Product;

enum ProductType: string
{
    case PRODUCT = Product::class;

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => 'Product',
        };
    }

    public function id(): string
    {
        return match ($this) {
            self::PRODUCT => 'product',
        };
    }

    public function getById(string $id): ?self
    {
        return match ($id) {
            'product' => self::PRODUCT,
            default => null,
        };
    }
}