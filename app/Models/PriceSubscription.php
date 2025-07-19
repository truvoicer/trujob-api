<?php

namespace App\Models;

use App\Enums\Price\PriceType;
use App\Enums\Subscription\SubscriptionSetupFeeFailureAction;
use App\Enums\Subscription\SubscriptionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_id',
        'name',
        'label',
        'description',
        'type',
        'start_time',
        'has_setup_fee',
        'setup_fee_value',
        'setup_fee_currency_id',
        'auto_bill_outstanding',
        'setup_fee_failure_action',
        'payment_failure_threshold',
    ];

    protected $casts = [
        'type' => SubscriptionType::class,
        'start_time' => 'datetime',
        'has_setup_fee' => 'boolean',
        'setup_fee_value' => 'decimal:2',
        'auto_bill_outstanding' => 'boolean',
        'setup_fee_failure_action' => SubscriptionSetupFeeFailureAction::class,
    ];

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function setupFeeCurrency()
    {
        return $this->belongsTo(Currency::class, 'setup_fee_currency_id');
    }

    public function items()
    {
        return $this->hasMany(PriceSubscriptionItem::class);
    }
}
