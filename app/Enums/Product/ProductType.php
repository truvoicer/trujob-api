<?php
namespace App\Enums\Product;

use App\Models\Listing;

enum ProductType: string
{
    case LISTING = Listing::class;

    public function label(): string
    {
        return match ($this) {
            self::LISTING => 'Listing',
        };
    }

    public function id(): string
    {
        return match ($this) {
            self::LISTING => 'listing',
        };
    }

    public function getById(string $id): ?self
    {
        return match ($id) {
            'listing' => self::LISTING,
            default => null,
        };
    }
}