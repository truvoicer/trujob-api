<?php

namespace App\Http\Controllers\Api\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use App\Services\PaymentMethod\PaymentMethodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends Controller
{

    public function __construct(
        private PaymentMethodService $paymentMethodService,
        private PaymentMethodRepository $paymentMethodRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->paymentMethodRepository->setPagination(true);
        $this->paymentMethodRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->paymentMethodRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->paymentMethodRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->paymentMethodRepository->setPage(
            $request->get('page', 1)
        );
        
        return PaymentMethodResource::collection(
            $this->paymentMethodRepository->findMany()
        );
    }

    public function view(PaymentMethod $paymentMethod, Request $request) {
        $this->paymentMethodService->setUser($request->user()->user);
        $this->paymentMethodService->setSite($request->user()->site);
        return new PaymentMethodResource($paymentMethod);
    }

    public function create(StorePaymentMethodRequest $request) {
        $this->paymentMethodService->setUser($request->user()->user);
        $this->paymentMethodService->setSite($request->user()->site);

        if (!$this->paymentMethodService->createPaymentMethod($request->validated())) {
            return response()->json([
                'message' => 'Error creating paymentMethod',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentMethod created',
        ], Response::HTTP_CREATED);
    }

    public function update(PaymentMethod $paymentMethod, UpdatePaymentMethodRequest $request) {
        $this->paymentMethodService->setUser($request->user()->user);
        $this->paymentMethodService->setSite($request->user()->site);

        if (!$this->paymentMethodService->updatePaymentMethod($paymentMethod, $request->validated())) {
            return response()->json([
                'message' => 'Error updating paymentMethod',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentMethod updated',
        ], Response::HTTP_OK);
    }
    public function destroy(PaymentMethod $paymentMethod, Request $request) {
        $this->paymentMethodService->setUser($request->user()->user);
        $this->paymentMethodService->setSite($request->user()->site);

        if (!$this->paymentMethodService->deletePaymentMethod($paymentMethod)) {
            return response()->json([
                'message' => 'Error deleting paymentMethod',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentMethod deleted',
        ], Response::HTTP_OK);
    }

}
