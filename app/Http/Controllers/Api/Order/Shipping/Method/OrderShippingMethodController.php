<?php

namespace App\Http\Controllers\Api\Order\Shipping\Method;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderShippingMethodResource;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderShippingMethodController extends Controller
{

    public function __construct(
        private OrderService $orderService,
        private OrderRepository $orderRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order, Request $request)
    {
        return ShippingMethodResource::collection(
            $order->availableShippingMethods()
        );
    }

    public function show(Order $order, ShippingMethod $shippingMethod, Request $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        return new ShippingMethodResource(
            $order->availableShippingMethods()->first()
        )->additional([
            'shipping_method' => $shippingMethod,
        ]);
    }
}
