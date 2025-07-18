<?php
namespace App\Enums\Subscription;

enum SubscriptionType: string
{
    case FIXED = 'FIXED';
    case REGULAR = 'REGULAR';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed',
            self::REGULAR => 'Regular',
        };
    }
}
