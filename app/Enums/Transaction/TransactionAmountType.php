<?php
namespace App\Enums\Transaction;

enum TransactionAmountType: string
{
    case SUBTOTAL = 'subtotal';
    case TAX = 'tax';
    case SHIPPING = 'shipping';
    case DISCOUNT = 'discount';
    case TOTAL = 'total';

    public function label(): string
    {
        return match ($this) {
            self::SUBTOTAL => 'Subtotal',
            self::TAX => 'Tax',
            self::SHIPPING => 'Shipping',
            self::DISCOUNT => 'Discount',
            self::TOTAL => 'Total',
        };
    }
}
