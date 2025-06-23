<?php
namespace App\Enums;

use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\PriceTaxRate;
use App\Models\Product;
use App\Models\Region;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction;
use App\Models\ShippingZone;
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
    case SHIPPING_ZONE = 'shipping_zone';
    case SHIPPING_METHOD = 'shipping_method';
    CASE TICKET = 'ticket';
    case CATEGORY = 'category';
    case PRODUCT = 'product';
    case CURRENCY = 'currency';
    case REGION = 'region';
    case COUNTRY = 'country';
    case TAX_RATE = 'tax_rate';
    case PRICE = 'price';

    public function getClass(): string
    {
        return match ($this) {
            self::SHIPPING_ZONE => ShippingZone::class,
            self::TAX_RATE => TaxRate::class,
            self::SITE => Site::class,
            self::SITE_USER => SiteUser::class,
            self::ORDER_ITEM => OrderItem::class,
            self::PRICE_TAX_RATE => PriceTaxRate::class,
            self::SHIPPING_METHOD => ShippingMethod::class,
            self::SHIPPING_RESTRICTION => ShippingRestriction::class,
            self::TICKET => Ticket::class,
            self::CATEGORY => Category::class,
            self::PRODUCT => Product::class,
            self::CURRENCY => Currency::class,
            self::REGION => Region::class,
            self::COUNTRY => Country::class,
            self::PRICE => Price::class,
        };
    }
}
