<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_method_id',
        'tracking_number',
        'status',
        'shipping_cost',
        'estimated_delivery_date',
        'actual_delivery_date',
        'ship_date',
        'weight',
        'dimensions',
        'notes'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'ship_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function markAsShipped(string $trackingNumber = null)
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber ?? $this->tracking_number,
            'ship_date' => now(),
        ]);
    }
}
