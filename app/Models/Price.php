<?php

namespace App\Models;

use App\Enums\Price\PriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'price_type',
        'country_id',
        'currency_id',
        'amount',
        'valid_from',
        'valid_to',
        'is_active',
    ];
    protected $casts = [
        'price_type' => PriceType::class,
        'amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_prices');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


    public function taxRates()
    {
        return $this->belongsToMany(TaxRate::class, 'price_tax_rates')
            ->withTimestamps();
    }

    public function defaultTaxRate()
    {
        return $this->taxRates()
            ->wherePivot('is_primary', true)
            ->first();
    }

    public function subscription()
    {
        return $this->hasOne(PriceSubscription::class);
    }
    public function priceSubscription()
    {
        return $this->hasOne(PriceSubscription::class);
    }

    public function discountables()
    {
        return $this->morphMany(Discountable::class, 'discountable');
    }

    public function calculateTax(float $price, Country $country, ?string $region = null)
    {
        $taxRate = $this->resolveApplicableTaxRate($country, $region);

        return $price * ($taxRate->rate / 100);
    }

    protected function resolveApplicableTaxRate(Country $country, ?string $region = null)
    {
        // First try price-specific rates
        if ($this->taxRates()->exists()) {
            $rate = $this->taxRates()
            ->whereHas('country', fn($q) => $q->where('code', $country->code))
                // ->orderBy('region', 'desc') // Prefer region-specific
                ->first();

            if ($rate) return $rate;
        }

        // Fall back to default rates for country/region
        return TaxRate::query()
            ->active()
            ->forRegion($countryCode ?? config('app.default_country'), $region)
            ->defaultRate()
            ->firstOrFail();
    }
}
