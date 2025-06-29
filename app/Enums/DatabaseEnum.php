<?php
namespace App\Enums;

use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\Shipping\OrderShipmentStatus;
use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\ShippingRestriction;
use App\Models\TaxRate;

enum DatabaseEnum: string
{
    case PRODUCT_TYPE = 'product_type';
    case PRODUCT_WEIGHT_UNIT = 'product_weight_unit';
    case PRODUCT_UNIT = 'product_unit';
    case ORDER_STATUS = 'order_status';
    case TAX_RATE_TYPE = 'tax_rate_type';
    case TAX_RATE_AMOUNT_TYPE = 'tax_rate_amount_type';
    case TAX_SCOPE = 'tax_scope';
    case DISCOUNT_TYPE = 'discount_type';
    case DISCOUNT_AMOUNT_TYPE = 'discount_amount_type';
    case DISCOUNT_SCOPE = 'discount_scope';
    case SHIPPING_RATE_TYPE = 'shipping_rate_type';
    case SHIPPING_UNIT = 'shipping_unit';
    case SHIPPING_WEIGHT_UNIT = 'shipping_weight_unit';
    case ORDER_SHIPMENT_STATUS = 'order_shipment_status';
    case SHIPPING_RESTRICTION_ACTION = 'shipping_restriction_action';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT_TYPE => 'Product Type',
            self::PRODUCT_WEIGHT_UNIT => 'Product Weight Unit',
            self::PRODUCT_UNIT => 'Product Unit',
            self::ORDER_STATUS => 'Order Status',
            self::TAX_RATE_TYPE => 'Tax Rate Type',
            self::TAX_RATE_AMOUNT_TYPE => 'Tax Rate Amount Type',
            self::TAX_SCOPE => 'Tax Scope',
            self::DISCOUNT_TYPE => 'Discount Type',
            self::DISCOUNT_AMOUNT_TYPE => 'Discount Amount Type',
            self::DISCOUNT_SCOPE => 'Discount Scope',
            self::SHIPPING_RATE_TYPE => 'Shipping Rate Type',
            self::SHIPPING_UNIT => 'Shipping Unit',
            self::SHIPPING_WEIGHT_UNIT => 'Shipping Weight Unit',
            self::ORDER_SHIPMENT_STATUS => 'Order Shipment Status',
            self::SHIPPING_RESTRICTION_ACTION => 'Shipping Restriction Action',
        };
    }

    public function enum(): string
    {
        return match ($this) {
            self::PRODUCT_TYPE => ProductType::class,
            self::PRODUCT_WEIGHT_UNIT => ProductWeightUnit::class,
            self::PRODUCT_UNIT => ProductUnit::class,
            self::ORDER_STATUS => OrderStatus::class,
            self::TAX_RATE_TYPE => TaxRateType::class,
            self::TAX_RATE_AMOUNT_TYPE => TaxRateAmountType::class,
            self::TAX_SCOPE => TaxScope::class,
            self::DISCOUNT_TYPE => DiscountType::class,
            self::DISCOUNT_AMOUNT_TYPE => DiscountAmountType::class,
            self::DISCOUNT_SCOPE => DiscountScope::class,
            self::SHIPPING_RATE_TYPE => ShippingRateType::class,
            self::SHIPPING_UNIT => ShippingUnit::class,
            self::SHIPPING_WEIGHT_UNIT => ShippingWeightUnit::class,
            self::ORDER_SHIPMENT_STATUS => OrderShipmentStatus::class,
            self::SHIPPING_RESTRICTION_ACTION => ShippingRestrictionAction::class,
        };
    }

    public function model(): array
    {
        return match ($this) {
            self::PRODUCT_TYPE => [
                Product::class => ['type']
            ],
            self::PRODUCT_WEIGHT_UNIT => [
                Product::class => ['weight_unit']
            ],
            self::PRODUCT_UNIT => [
                Product::class => ['height_unit', 'width_unit', 'length_unit']
            ],
            self::ORDER_STATUS => [
                Order::class => ['status']
            ],
            self::TAX_RATE_TYPE => [
                TaxRate::class => ['type']
            ],
            self::TAX_RATE_AMOUNT_TYPE => [
                TaxRate::class => ['amount_type']
            ],
            self::TAX_SCOPE => [
                TaxRate::class => ['scope']
            ],
            self::DISCOUNT_TYPE => [
                Discount::class => ['type']
            ],
            self::DISCOUNT_AMOUNT_TYPE => [
                Discount::class => ['amount_type']
            ],
            self::DISCOUNT_SCOPE => [
                Discount::class => ['scope']
            ],
            self::SHIPPING_RATE_TYPE => [
                ShippingRate::class => ['type']
            ],
            self::SHIPPING_UNIT => [
                ShippingRate::class => ['height_unit', 'width_unit', 'length_unit']
            ],
            self::SHIPPING_WEIGHT_UNIT => [
                ShippingRate::class => ['weight_unit']
            ],
            self::ORDER_SHIPMENT_STATUS => [
                OrderShipment::class => ['status']
            ],
            self::SHIPPING_RESTRICTION_ACTION => [
                ShippingRestriction::class => ['action']
            ],
        };
    }
}
