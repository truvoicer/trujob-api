<?php

namespace App\Models;

use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
       use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'type',
        'amount_type',
        'amount',
        'rate',
        'currency_id',
        'is_active',
        'scope'
    ];

    protected $casts = [
        'amount_type' => TaxRateAmountType::class,
        'type' => TaxRateType::class,
        'scope' => TaxScope::class,
        'rate' => 'decimal:5',
        'is_active' => 'boolean',
        'has_region' => 'boolean',
    ];

    public function prices()
    {
        return $this->belongsToMany(Product::class, 'price_tax_rates')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function default() {
        return $this->hasOne(DefaultTaxRate::class);
    }

    public function isDefault()
    {
        return $this->default()->exists();
    }

    public function taxRateAbles()
    {
        return $this->morphMany(TaxRateAble::class, 'tax_rateable');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        return true;
    }
}
