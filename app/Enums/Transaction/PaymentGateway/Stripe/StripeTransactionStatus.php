<?php
namespace App\Enums\Transaction\PaymentGateway\Stripe;

enum StripeTransactionStatus: string
{
    case COMPLETE = 'complete';
    case EXPIRED = 'expired';
    case OPEN = 'open';

    public function label(): string
    {
        return match ($this) {
            self::COMPLETE => 'Complete',
            self::EXPIRED => 'Expired',
            self::OPEN => 'Open',
        };
    }
}
