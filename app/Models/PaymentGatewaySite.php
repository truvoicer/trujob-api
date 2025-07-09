<?php

namespace App\Models;

use App\Enums\Payment\PaymentGatewayEnvironment;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PaymentGatewaySite extends Pivot
{
    //

    protected $fillable = [
        'site_id',
        'payment_gateway_id',
        'settings',
        'is_active',
        'is_default',
        'environment',
        'required_fields',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'environment' => PaymentGatewayEnvironment::class,
    ];

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
