<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\PriceType\OneTime\OneTimeOrderResource;
use App\Http\Resources\Order\PriceType\Subscription\SubscriptionOrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
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
    public function index(Request $request)
    {
        $this->orderRepository->setPagination(true);
        $this->orderRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->orderRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->orderRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->orderRepository->setPage(
            $request->get('page', 1)
        );

        return OrderResource::collection(
            $this->orderRepository->findMany()
        );
    }

    public function show(Order $order, Request $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                return new OneTimeOrderResource(
                    $this->orderService->getOrderByPriceType($order)
                );
            case PriceType::SUBSCRIPTION:
                return new SubscriptionOrderResource(
                    $this->orderService->getOrderByPriceType($order)
                );
            default:
                throw new \Exception('Invalid price type');
        }
    }

    public function store(Order $order, StoreOrderRequest $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        $order = $this->orderService->createOrder($request->validated());
        if (!$order || !$order->exists()) {
            return response()->json([
                'message' => 'Error creating order order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $getOrder = $order->loadOrderItemsByPriceType(
            $order->price_type,
            $request->user()->user
        );
        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                return new OneTimeOrderResource($getOrder);
            case PriceType::SUBSCRIPTION:
                return new SubscriptionOrderResource(
                    $getOrder
                );
            default:
                throw new \Exception('Invalid price type');
        }
    }


    public function update(Order $order, UpdateOrderRequest $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        if (!$this->orderService->updateOrder($order, $request->validated())) {
            return response()->json([
                'message' => 'Error updating order order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order order updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, Request $request)
    {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        if (!$this->orderService->deleteOrder($order)) {
            return response()->json([
                'message' => 'Error deleting order order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order order deleted',
        ], Response::HTTP_OK);
    }
}
