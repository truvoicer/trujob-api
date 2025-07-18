<?php
namespace App\Enums\Subscription;

enum SubscriptionSetupFeeFailureAction: string
{
    case CONTINUE = 'CONTINUE';
    case CANCEL = 'CANCEL';

    public function label(): string
    {
        return match ($this) {
            self::CONTINUE => 'Continue',
            self::CANCEL => 'Cancel',
        };
    }
}
