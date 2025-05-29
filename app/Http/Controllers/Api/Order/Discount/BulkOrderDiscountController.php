<?php

namespace App\Http\Controllers\Api\Order\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Discount\StoreBulkOrderDiscountRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Symfony\Component\HttpFoundation\Response;

class BulkOrderDiscountController extends Controller
{

    public function __construct(
        private OrderService $orderService,
        private OrderRepository $orderRepository,
    )
    {
    }
    public function __invoke(Order $order, StoreBulkOrderDiscountRequest $request) {
        $this->orderService->setUser($request->user()->user);
        $this->orderService->setSite($request->user()->site);
        if (!$this->orderService->syncDiscounts($order, $request->validated('ids'))) {
            return response()->json([
                'message' => 'Error syncing discount to order',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Order discount synced successfully',
        ], Response::HTTP_OK);
    }

}
