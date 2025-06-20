<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaxRateAble extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_rate_id',
        'tax_rateable_id',
        'tax_rateable_type',
    ];

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function taxRateable(): MorphTo
    {
        return $this->morphTo();
    }
}
