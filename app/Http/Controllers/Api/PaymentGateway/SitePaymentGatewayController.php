<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGateway\StoreSitePaymentGatewayRequest;
use App\Http\Requests\PaymentGateway\UpdateSitePaymentGatewayRequest;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Http\Resources\PaymentGateway\SitePaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Repositories\PaymentGatewayRepository;
use App\Services\PaymentGateway\SitePaymentGatewayService;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SitePaymentGatewayController extends Controller
{

    public function __construct(
        private SitePaymentGatewayService $sitePaymentGatewayService,
        private PaymentGatewayRepository $paymentGatewayRepository,
    ) {}

    public function index(
        Request $request
    ) {
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

        $this->paymentGatewayRepository->setWith([
            'sites' => function ($query) use ($request) {
                $query->where('site_id', $request->user()->site->id);
            }
        ]);

        return SitePaymentGatewayResource::collection(
            $this->paymentGatewayRepository->findMany()
        );
    }

    public function show(
        PaymentGateway $paymentGateway,
        Request $request
    ) {
        $this->sitePaymentGatewayService->setUser($request->user()->user);
        $this->sitePaymentGatewayService->setSite($request->user()->site);
        $findPaymentGateway = $request->user()->site->paymentGateways()->find($paymentGateway->id);
        if (!$findPaymentGateway) {
            return response()->json([
                'message' => 'Payment gateway not found for this site',
            ], Response::HTTP_NOT_FOUND);
        }
        return new SitePaymentGatewayResource($findPaymentGateway);
    }


    public function update(
        PaymentGateway $paymentGateway,
        UpdateSitePaymentGatewayRequest $request
    ) {
        $this->sitePaymentGatewayService->setUser($request->user()->user);
        $this->sitePaymentGatewayService->setSite($request->user()->site);

        if (!$this->sitePaymentGatewayService->savePaymentGateway(
            $paymentGateway,
            $request->validated()
        )) {
            return response()->json([
                'message' => 'Error updating paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway updated',
        ], Response::HTTP_OK);
    }

    public function destroy(
        PaymentGateway $paymentGateway,
        Request $request
    ) {
        $this->sitePaymentGatewayService->setUser($request->user()->user);
        $this->sitePaymentGatewayService->setSite($request->user()->site);

        if (!$this->sitePaymentGatewayService->deletePaymentGateway(
            $paymentGateway
        )) {
            return response()->json([
                'message' => 'Error deleting paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway deleted',
        ], Response::HTTP_OK);
    }
}
