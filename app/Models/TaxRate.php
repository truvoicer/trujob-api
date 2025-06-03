<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
       use HasFactory;

    protected $fillable = [
        'name',
        'type',
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
}
