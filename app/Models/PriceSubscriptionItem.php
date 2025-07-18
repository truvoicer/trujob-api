<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSubscriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_subscription_id',
        'frequency_interval_unit',
        'frequency_interval_count',
        'tenure_type',
        'sequence',
        'total_cycles',
        'price_value',
        'price_currency_id'
    ];

    protected $casts = [
        'price_value' => 'decimal:2',
    ];

    public function priceSubscription()
    {
        return $this->belongsTo(PriceSubscription::class);
    }

    public function priceCurrency()
    {
        return $this->belongsTo(Currency::class, 'price_currency_id');
    }

}
