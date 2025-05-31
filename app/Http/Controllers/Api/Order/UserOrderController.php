<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Order\StoreOrderRequest;
use App\Http\Requests\Order\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOrderController extends Controller
{

    public function __construct(
        private OrderService $orderService,
        private OrderRepository $orderRepository,
    )
    {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $product, Request $request) {
        $this->orderRepository->setQuery(
            $request->user()->user->orders()
        );
        $this->orderRepository->setPagination(true);
        $this->orderRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->orderRepository->setOrderDir(
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

    public function show(Order $product, Order $order, Request $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        $check = $product->orders()->where('orders.id', $order->id)->first();
        if (!$check) {
            return response()->json([
                'message' => 'Order not found in product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new OrderResource($order);
    }

    public function store(Order $product, StoreOrderRequest $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        if (!$this->orderService->createOrder($product, $request->validated())) {
            return response()->json([
                'message' => 'Error creating product order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order order created',
        ], Response::HTTP_CREATED);
    }

    
    public function update(Order $product, Order $order, UpdateOrderRequest $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);

        if (!$this->orderService->updateOrder($product, $order, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order order updated',
        ], Response::HTTP_OK);
    }
    
    public function destroy(Order $product, Order $order, Request $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        
        if (!$this->orderService->deleteOrder($product, $order)) {
            return response()->json([
                'message' => 'Error deleting product order',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order order deleted',
        ], Response::HTTP_OK);
    }
}
