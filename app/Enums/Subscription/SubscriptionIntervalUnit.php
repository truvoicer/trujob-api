<?php
namespace App\Enums\Subscription;

enum SubscriptionIntervalUnit: string
{
    case MONTH = 'MONTH';
    case WEEK = 'WEEK';
    case DAY = 'DAY';
    case YEAR = 'YEAR';

    public function label(): string
    {
        return match ($this) {
            self::MONTH => 'Month',
            self::WEEK => 'Week',
            self::DAY => 'Day',
            self::YEAR => 'Year',
        };
    }
}
