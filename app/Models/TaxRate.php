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
        'type',
        'amount_type',
        'rate',
        'country_id',
        'currency_id',
        'has_region',
        'region',
        'is_default',
        'applies_to',
        'is_active'
    ];

    protected $casts = [
        'amount_type' => TaxRateAmountType::class,
        'type' => TaxRateType::class,
        'scope' => TaxScope::class,
        'rate' => 'decimal:5',
        'is_default' => 'boolean',
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

    public function scopeForCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeDefaultRate($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForRegion($query, string $countryCode, ?string $region = null)
    {
        return $query->where('country_code', $countryCode)
            ->where(function($q) use ($region) {
                $q->where('region', $region)
                  ->orWhereNull('region');
            })
            ->orderByDesc('region'); // Prefer region-specific rates over null
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }
}
