<?php
namespace App\Enums\Price;

enum PriceType: string
{
    case SUBSCRIPTION = 'subscription';
    case ONE_TIME = 'one_time';
    case RECURRING = 'recurring';
    case FREE = 'free';
    case DONATION = 'donation';
    case GIFT_CARD = 'gift_card';
    case CREDIT = 'credit';
    case DISCOUNT = 'discount';
    case COUPON = 'coupon';
    case VOUCHER = 'voucher';
    case TRIAL = 'trial';
    case PRE_ORDER = 'pre_order';
    case BACK_ORDER = 'back_order';
    case LAYAWAY = 'layaway';
    case RENTAL = 'rental';
    case LEASE = 'lease';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'Subscription',
            self::ONE_TIME => 'One Time',
            self::RECURRING => 'Recurring',
            self::FREE => 'Free',
            self::DONATION => 'Donation',
            self::GIFT_CARD => 'Gift Card',
            self::CREDIT => 'Credit',
            self::DISCOUNT => 'Discount',
            self::COUPON => 'Coupon',
            self::VOUCHER => 'Voucher',
            self::TRIAL => 'Trial',
            self::PRE_ORDER => 'Pre Order',
            self::BACK_ORDER => 'Back Order',
            self::LAYAWAY => 'Layaway',
            self::RENTAL => 'Rental',
            self::LEASE => 'Lease',
        };
    }
}
