<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderSummaryResource;
use App\Models\Product;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderSummaryController extends Controller
{

    public function __construct(
        private OrderService $orderService,
        private OrderRepository $orderRepository,
    ) {}


    public function show(Order $order, Request $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        return new OrderSummaryResource(
            $order->load([
                'items',
            ])
        );
    }

}
