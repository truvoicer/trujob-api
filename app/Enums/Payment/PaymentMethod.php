<?php
namespace App\Enums\Payment;

enum PaymentMethod: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CRYPTOCURRENCY = 'cryptocurrency';
}