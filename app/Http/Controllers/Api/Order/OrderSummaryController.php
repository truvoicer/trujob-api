<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\PriceType\OneTime\OneTimeOrderSummaryResource;
use App\Http\Resources\Order\PriceType\Subscription\SubscriptionOrderSummaryResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

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
        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                return new OneTimeOrderSummaryResource(
                    $order->load([
                        'items',
                    ])
                );
            case PriceType::SUBSCRIPTION:
                return new SubscriptionOrderSummaryResource(
                    $order->load([
                        'items',
                    ])
                );
            default:
                throw new \Exception('Invalid price type');
        }
    }
}
