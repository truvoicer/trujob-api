<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayPaymentMethod extends Model
{
    
    public function paymentGateway() {
        return $this->belongsTo(PaymentGateway::class);
    }
    
    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }
}
