<?php

namespace App\Services\Order\Shipment;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Services\BaseService;

class OrderShipmentService extends BaseService
{

    public function createOrderShipment(Order $order, array $data) {


        $method = ShippingMethod::find($data['shipping_method_id']);
        if (!$method) {
            throw new \Exception('Shipping method not found');
        }
        $zone = $order->shippingZone; // Assuming you have this relationship

        $rate = ShippingRate::where('shipping_method_id', $method->id)
            ->where('shipping_zone_id', $zone->id)
            ->firstOrFail();

        $orderShipment = $order->shipments()->create([
            'shipping_method_id' => $method->id,
            'tracking_number' => $data['tracking_number'],
            'status' => 'processing',
            'shipping_cost' => $rate->calculateRate($order->total, $data['weight'] ?? 0),
            'currency_code' => $rate->currency_code,
            'weight' => $data['weight'],
            'dimensions' => $data['dimensions'],
            'notes' => $data['notes'],
            'estimated_delivery_date' => now()->addDays($method->processing_time_days + 3),
        ]);
        if (!$orderShipment->exists()) {
            throw new \Exception('Error creating order shipment');
        }
        $orderShipment->refresh();
        return $orderShipment;
    }
    public function updateOrderShipment(Order $order, OrderShipment $orderShipment, array $data) {
        if (!$orderShipment->update($data)) {
            throw new \Exception('Error updating order shipment');
        }

        if (array_key_exists('status', $data) && $data['status'] === 'shipped') {
            $orderShipment->update(['ship_date' => now()]);
        }

        if (array_key_exists('status', $data) && $data['status'] === 'delivered') {
            $orderShipment->update(['actual_delivery_date' => now()]);
        }
        return $orderShipment;
    }

    public function deleteOrderShipment(Order $order, OrderShipment $orderShipment) {
        if (!$orderShipment->delete()) {
            throw new \Exception('Error deleting order shipment');
        }
        return true;
    }

    public function markAsShipped(OrderShipment $shipment)
    {
        $shipment->markAsShipped();
        return true;
    }

    public function markAsDelivered(OrderShipment $shipment)
    {
        $shipment->update([
            'status' => 'delivered',
            'actual_delivery_date' => now()
        ]);
        return true;
    }

}
