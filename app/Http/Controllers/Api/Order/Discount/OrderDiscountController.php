<?php

namespace App\Http\Controllers\Api\Order\Discount;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountResource;
use App\Models\Discount;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderDiscountController extends Controller
{

    public function __construct(
        private OrderService $orderService,
        private OrderRepository $orderRepository,
    )
    {
    }
    public function index(Order $order, Request $request) {
        $this->orderRepository->setQuery(
            $order->discounts()
        );
        $this->orderRepository->setPagination(true);
        $this->orderRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->orderRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->orderRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->orderRepository->setPage(
            $request->get('page', 1)
        );
        
        return DiscountResource::collection(
            $this->orderRepository->findMany()
        );
    }

    public function store(Order $order, Discount $discount, Request $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        if ($order->discounts()->where('discounts.id', $discount->id)->exists()) {
            return response()->json([
                'message' => 'Discount already exists in order',
            ], Response::HTTP_BAD_REQUEST);
        }
        $order->discounts()->attach(
            $discount->id
        );
        return response()->json([
            'message' => 'Order discount created',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, Discount $discount, Request $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        
        $order->discounts()->detach(
            $discount->id
        );

        return response()->json([
            'message' => 'Order discount deleted',
        ], Response::HTTP_OK);
    }
}
