<?php
namespace App\Enums\Payment;

enum PaymentGateway: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CRYPTOCURRENCY = 'cryptocurrency';

    public function label(): string
    {
        return match ($this) {
            self::STRIPE => 'Stripe',
            self::PAYPAL => 'PayPal',
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CRYPTOCURRENCY => 'Cryptocurrency',
        };
    }
}
