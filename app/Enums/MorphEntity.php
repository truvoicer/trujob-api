<?php
namespace App\Enums;

use App\Models\OrderItem;
use App\Models\PriceTaxRate;
use App\Models\ShippingRestriction;
use App\Models\SiteUser;
use App\Models\Ticket;

enum MorphEntity: string
{
    case SITE_USER = 'site_user';
    case ORDER_ITEM = 'order_item';
    case PRICE_TAX_RATE = 'price_tax_rate';
    case SHIPPING_RESTRICTION = 'shipping_restriction';
    CASE TICKET = 'ticket';

    public function getClass(): string
    {
        return match ($this) {
            self::SITE_USER => SiteUser::class,
            self::ORDER_ITEM => OrderItem::class,
            self::PRICE_TAX_RATE => PriceTaxRate::class,
            self::SHIPPING_RESTRICTION => ShippingRestriction::class,
            self::TICKET => Ticket::class,
        };
    }
}
