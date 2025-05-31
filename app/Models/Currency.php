<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'name',
        'name_plural',
        'code',
        'symbol'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function productTransaction()
    {
        return $this->belongsTo(ProductPrice::class);
    }


}
