<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentGateway\SitePaymentGatewayResource;
use App\Repositories\PaymentGatewayRepository;
use App\Services\PaymentGateway\SitePaymentGatewayService;
use Illuminate\Http\Request;

class AvailableSitePaymentGatewayController extends Controller
{

    public function __construct(
        private SitePaymentGatewayService $sitePaymentGatewayService,
        private PaymentGatewayRepository $paymentGatewayRepository,
    ) {}

    public function index(
        Request $request
    ) {
        $this->paymentGatewayRepository->setQuery(
            $request->user()->site->paymentGateways()
        );
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

        return SitePaymentGatewayResource::collection(
            $this->paymentGatewayRepository->findMany()
        );
    }

}
