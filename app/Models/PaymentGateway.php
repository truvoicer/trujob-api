<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'icon',
        'is_default',
        'is_active',
        'settings',
        'required_fields',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
        'required_fields' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_gateway_payment_methods');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'payment_gateway_sites')
        ->using(PaymentGatewaySite::class)
            ->withPivot('settings', 'is_active', 'is_default', 'environment')
            ->withTimestamps();
    }

}
