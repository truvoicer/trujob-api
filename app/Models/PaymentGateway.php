<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_default',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_gateway_payment_methods');
    }
}
