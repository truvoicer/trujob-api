<?php

namespace App\Models;

use App\Enums\Order\OrderStatus;
use App\Enums\Price\PriceType;
use App\Traits\Model\Order\CalculateOrderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    use CalculateOrderTrait;

    protected $fillable = [
        'price_type',
        'user_id',
        'country_id',
        'currency_id',
        'billing_address_id',
        'shipping_address_id',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'price_type' => PriceType::class,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function orderTransactions() {
        return $this->hasMany(OrderTransaction::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'order_transactions')
            ->withTimestamps();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_orders')
            ->withTimestamps();
    }

    public function loadSubscriptionOrderItems(User $user)
    {
        return $this->load([
            'items' => function ($query) use ($user) {
                $query->with([
                    'orderItemable' => function ($query) use ($user) {
                        $query->with([
                            'prices' => function ($query) use ($user) {
                                $query->where(
                                    'price_type',
                                    PriceType::SUBSCRIPTION->value
                                )
                                    ->where('is_active', true)
                                    ->where('currency_id', $user->userSetting?->currency?->id ?? null)
                                    ->where('country_id', $user->userSetting?->country?->id ?? null);
                            },
                        ]);
                    },
                ]);
            },
        ]);
    }

    public function loadOrderItemsByPriceType(PriceType $priceType, User $user)
    {
        switch ($priceType) {
            case PriceType::SUBSCRIPTION:
                return $this->loadSubscriptionOrderItems($user);
            case PriceType::ONE_TIME:
                return $this->load(['items']);
            default:
                throw new \Exception('Invalid price type');
        }
    }

}
