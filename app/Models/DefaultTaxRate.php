<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTaxRate extends Model
{

    protected $table = 'default_tax_rates';
    
    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }
}
