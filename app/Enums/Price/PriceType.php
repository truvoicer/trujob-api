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
}