<?php

namespace App\Http\Controllers\Api\PaymentGateway\Stripe;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGateway\StorePaymentGatewayRequest;
use App\Http\Requests\PaymentGateway\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Repositories\PaymentGatewayRepository;
use App\Services\PaymentGateway\PaymentGatewayService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StripeOrderController extends Controller
{

    public function __construct(
        private PaymentGatewayService $paymentGatewayService,
        private PaymentGatewayRepository $paymentGatewayRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->paymentGatewayRepository->setPagination(true);
        $this->paymentGatewayRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->paymentGatewayRepository->setOrderByDir(
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

}
