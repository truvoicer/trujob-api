<?php
namespace App\Enums\Transaction\PaymentGateway\Stripe;

enum StripeTransactionPaymentStatus: string
{
    case NO_PAYMENT_REQUIRED = 'no_payment_required';
    case PAID = 'paid';
    case UNPAID = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::NO_PAYMENT_REQUIRED => 'No Payment Required',
            self::PAID => 'Paid',
            self::UNPAID => 'Unpaid',
        };
    }
}
