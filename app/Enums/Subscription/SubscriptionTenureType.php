<?php
namespace App\Enums\Subscription;

enum SubscriptionTenureType: string
{
    case TRIAL = 'TRIAL';
    case REGULAR = 'REGULAR';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Trial',
            self::REGULAR => 'Regular',
        };
    }
}
