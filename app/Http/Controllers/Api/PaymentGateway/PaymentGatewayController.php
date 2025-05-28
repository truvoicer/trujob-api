<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGateway\StorePaymentGatewayRequest;
use App\Http\Requests\PaymentGateway\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Repositories\PaymentGatewayRepository;
use App\Services\PaymentGateway\PaymentGatewayService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentGatewayController extends Controller
{

    public function __construct(
        private PaymentGatewayService $paymentGatewayService,
        private PaymentGatewayRepository $paymentGatewayRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->paymentGatewayRepository->setPagination(true);
        $this->paymentGatewayRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->paymentGatewayRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->paymentGatewayRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->paymentGatewayRepository->setPage(
            $request->get('page', 1)
        );
        
        return PaymentGatewayResource::collection(
            $this->paymentGatewayRepository->findMany()
        );
    }

    public function show(PaymentGateway $paymentGateway, Request $request) {
        $this->paymentGatewayService->setUser($request->user()->user);
        $this->paymentGatewayService->setSite($request->user()->site);
        return new PaymentGatewayResource($paymentGateway);
    }

    public function store(StorePaymentGatewayRequest $request) {
        $this->paymentGatewayService->setUser($request->user()->user);
        $this->paymentGatewayService->setSite($request->user()->site);

        if (!$this->paymentGatewayService->createPaymentGateway($request->validated())) {
            return response()->json([
                'message' => 'Error creating paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway created',
        ], Response::HTTP_CREATED);
    }

    public function update(PaymentGateway $paymentGateway, UpdatePaymentGatewayRequest $request) {
        $this->paymentGatewayService->setUser($request->user()->user);
        $this->paymentGatewayService->setSite($request->user()->site);

        if (!$this->paymentGatewayService->updatePaymentGateway($paymentGateway, $request->validated())) {
            return response()->json([
                'message' => 'Error updating paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway updated',
        ], Response::HTTP_OK);
    }
    public function destroy(PaymentGateway $paymentGateway, Request $request) {
        $this->paymentGatewayService->setUser($request->user()->user);
        $this->paymentGatewayService->setSite($request->user()->site);

        if (!$this->paymentGatewayService->deletePaymentGateway($paymentGateway)) {
            return response()->json([
                'message' => 'Error deleting paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway deleted',
        ], Response::HTTP_OK);
    }

}
