<?php

namespace App\Models;

use App\Enums\Price\PriceType;
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
      'setup_fee_value',
      'setup_fee_currency_id',
    ];

    protected $casts = [
        'setup_fee_value' => 'decimal:2',
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
