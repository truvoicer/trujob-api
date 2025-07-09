<?php

namespace App\Enums\Payment;

enum PaymentGatewayEnvironment: string
{
    case SANDBOX = 'sandbox';
    case PRODUCTION = 'production';
}
