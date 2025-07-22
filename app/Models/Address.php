<?php

namespace App\Models;

use App\Enums\Locale\AddressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'country_id',
        'label',
        'is_default',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'phone',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'type' => AddressType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
