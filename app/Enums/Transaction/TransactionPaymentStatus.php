<?php
namespace App\Enums\Transaction;

enum TransactionPaymentStatus: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    case REQUIRES_CAPTURE = 'requires_capture';

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::UNPAID => 'Unpaid',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::REQUIRES_PAYMENT_METHOD => 'Requires Payment Method',
            self::REQUIRES_CAPTURE => 'Requires Capture',
        };
    }
}
