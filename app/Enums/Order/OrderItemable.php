<?php
namespace App\Enums\Order;


enum OrderItemable: string
{
    case PRODUCT = 'product';

    public function id(): string
    {
        return match ($this) {
            self::PRODUCT => 'product',
        };
    }
}
