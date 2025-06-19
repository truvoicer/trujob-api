<?php
namespace App\Enums;

use App\Models\OrderItem;
use App\Models\PriceTaxRate;
use App\Models\ShippingRestriction;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\TaxRate;
use App\Models\Ticket;

enum MorphEntity: string
{
    case SITE = 'site';
    case SITE_USER = 'site_user';
    case ORDER_ITEM = 'order_item';
    case PRICE_TAX_RATE = 'price_tax_rate';
    case SHIPPING_RESTRICTION = 'shipping_restriction';
    CASE TICKET = 'ticket';
    case CATEGORY = 'category';
    case PRODUCT = 'product';
    case CURRENCY = 'currency';
    case REGION = 'region';
    case COUNTRY = 'country';
    case TAX_RATE = 'tax_rate';

    public function getClass(): string
    {
        return match ($this) {
            self::TAX_RATE => TaxRate::class,
            self::SITE => Site::class,
            self::SITE_USER => SiteUser::class,
            self::ORDER_ITEM => OrderItem::class,
            self::PRICE_TAX_RATE => PriceTaxRate::class,
            self::SHIPPING_RESTRICTION => ShippingRestriction::class,
            self::TICKET => Ticket::class,
            self::CATEGORY => \App\Models\Category::class,
            self::PRODUCT => \App\Models\Product::class,
            self::CURRENCY => \App\Models\Currency::class,
            self::REGION => \App\Models\Region::class,
            self::COUNTRY => \App\Models\Country::class,
        };
    }
}
