<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PriceTaxRate extends Model
{

    protected $table = 'price_tax_rates';

    protected $fillable = [
        'price_id',
        'tax_rate_id',
    ];

    public function productTaxRateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }
}
