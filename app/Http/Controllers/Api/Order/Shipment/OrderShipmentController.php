<?php

namespace App\Http\Controllers\Api\Order\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Shipment\StoreOrderShipmentRequest;
use App\Http\Requests\Order\Shipment\UpdateOrderShipmentRequest;
use App\Http\Resources\Order\OrderShipmentResource;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Repositories\OrderShipmentRepository;
use App\Services\Order\Shipment\OrderShipmentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderShipmentController extends Controller
{

    public function __construct(
        private OrderShipmentService $orderShipmentService,
        private OrderShipmentRepository $orderShipmentRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order, Request $request)
    {
        $this->orderShipmentRepository->setQuery(
            $order->items()
        );
        $this->orderShipmentRepository->setPagination(true);
        $this->orderShipmentRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->orderShipmentRepository->setOrderDir(
            $request->get('orderShipment', 'desc')
        );
        $this->orderShipmentRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->orderShipmentRepository->setPage(
            $request->get('page', 1)
        );

        return OrderShipmentResource::collection(
            $this->orderShipmentRepository->findMany()
        );
    }

    public function show(Order $order, OrderShipment $orderShipment, Request $request)
    {
        $this->orderShipmentService->setUser($request->user()->user);
        $this->orderShipmentService->setSite($request->user()->site);

        $check = $order->items()->where('id', $orderShipment->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Shipment not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new OrderShipmentResource(
            $orderShipment
        );
    }

    public function store(Order $order, StoreOrderShipmentRequest $request)
    {
        $this->orderShipmentService->setUser($request->user()->user);
        $this->orderShipmentService->setSite($request->user()->site);
        $orderShipment = $this->orderShipmentService->createOrderShipment($order, $request->validated());
        if (!$orderShipment || !$orderShipment->exists()) {
            return response()->json([
                'message' => 'Error creating order Shipment',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return OrderShipmentResource::make(
            $orderShipment
        );
    }


    public function update(Order $order, OrderShipment $orderShipment, UpdateOrderShipmentRequest $request)
    {
        $this->orderShipmentService->setUser($request->user()->user);
        $this->orderShipmentService->setSite($request->user()->site);

        $check = $order->items()->where('id', $orderShipment->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Shipment not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$this->orderShipmentService->updateOrderShipment($order, $orderShipment, $request->validated())) {
            return response()->json([
                'message' => 'Error updating order Shipment',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order Shipment updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, OrderShipment $orderShipment, Request $request)
    {
        $this->orderShipmentService->setUser($request->user()->user);
        $this->orderShipmentService->setSite($request->user()->site);
        $check = $order->items()->where('id', $orderShipment->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Order Shipment not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$this->orderShipmentService->deleteOrderShipment($order, $orderShipment)) {
            return response()->json([
                'message' => 'Error deleting order Shipment',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Order Shipment deleted',
        ], Response::HTTP_OK);
    }
}
