<?php

namespace App\Http\Controllers\Api\Order\Item;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Item\StoreOrderItemRequest;
use App\Http\Requests\Order\Item\UpdateOrderItemRequest;
use App\Http\Resources\Order\OrderItemResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use App\Services\Order\Item\OrderItemService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderItemController extends Controller
{

    public function __construct(
        private OrderItemService $orderItemService,
        private OrderItemRepository $orderItemRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order, Request $request)
    {
        $this->orderItemRepository->setQuery(
            $order->items()
        );
        $this->orderItemRepository->setPagination(true);
        $this->orderItemRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->orderItemRepository->setOrderDir(
            $request->get('orderItem', 'desc')
        );
        $this->orderItemRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->orderItemRepository->setPage(
            $request->get('page', 1)
        );

        return OrderItemResource::collection(
            $this->orderItemRepository->findMany()
        );
    }

    public function show(Order $order, OrderItem $orderItem, Request $request)
    {
        $this->orderItemService->setUser($request->user()->user);
        $this->orderItemService->setSite($request->user()->site);

        $check = $order->items()->where('id', $orderItem->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Item not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new OrderItemResource(
            $orderItem
        );
    }

    public function store(Order $order, StoreOrderItemRequest $request)
    {
        $this->orderItemService->setUser($request->user()->user);
        $this->orderItemService->setSite($request->user()->site);
        $orderItem = $this->orderItemService->createOrderItem($order, $request->validated());
        if (!$orderItem || !$orderItem->exists()) {
            return response()->json([
                'message' => 'Error creating order Item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return OrderItemResource::make(
            $orderItem
        );
    }


    public function update(Order $order, OrderItem $orderItem, UpdateOrderItemRequest $request)
    {
        $this->orderItemService->setUser($request->user()->user);
        $this->orderItemService->setSite($request->user()->site);

        $check = $order->items()->where('id', $orderItem->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Item not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$this->orderItemService->updateOrderItem($order, $orderItem, $request->validated())) {
            return response()->json([
                'message' => 'Error updating order Item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order Item updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, OrderItem $orderItem, Request $request)
    {
        $this->orderItemService->setUser($request->user()->user);
        $this->orderItemService->setSite($request->user()->site);
        $check = $order->items()->where('id', $orderItem->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Item not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$this->orderItemService->deleteOrderItem($orderItem)) {
            return response()->json([
                'message' => 'Error deleting order Item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order Item deleted',
        ], Response::HTTP_OK);
    }
}
