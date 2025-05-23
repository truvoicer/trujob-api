<?php

namespace App\Enums\Locale;

enum AddressType: string
{
    case BILLING = 'billing';
    case SHIPPING = 'shipping';
}