<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaxRateLocale extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_rate_id',
        'localeable_id',
        'localeable_type',
    ];

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function localeable(): MorphTo
    {
        return $this->morphTo();
    }
}
