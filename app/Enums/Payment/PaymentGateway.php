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

    public function description(): string
    {
        return match ($this) {
            self::STRIPE => 'Online payment processing for internet businesses.',
            self::PAYPAL => 'A fast and secure way to pay online.',
            self::CASH => 'Physical cash payment method.',
            self::BANK_TRANSFER => 'Direct transfer from one bank account to another.',
            self::CRYPTOCURRENCY => 'Digital or virtual currency that uses cryptography for security.',
        };
    }

    public function isDefault(): bool
    {
        return $this === self::STRIPE; // Assuming Stripe is the default payment gateway
    }
}
