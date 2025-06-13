<?php

namespace App\Http\Controllers\Api\Price\TaxRate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Price\Tax\StorePriceTaxRateRequest;
use App\Http\Requests\Price\Tax\UpdatePriceTaxRateRequest;
use App\Http\Resources\Price\PriceTaxRateResource;
use App\Models\Price;
use App\Models\PriceTaxRate;
use App\Models\TaxRate;
use App\Repositories\PriceTaxRateRepository;
use App\Services\Price\PriceTaxRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceTaxRateController extends Controller
{
    // This controller is responsible for handling priceTaxRate-related operations.
    // It will contain methods to create, update, delete, and retrieve priceTaxRates.
    // The methods will interact with the PriceTaxRateService to perform the necessary operations.

    public function __construct(
        private PriceTaxRateService $priceTaxRateService,
        private PriceTaxRateRepository $priceTaxRateRepository

    ) {}

    public function index(Price $price, Request $request)
    {

        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);

        $this->priceTaxRateRepository->setPagination(true);
        $this->priceTaxRateRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->priceTaxRateRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->priceTaxRateRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->priceTaxRateRepository->setPage(
            $request->get('page', 1)
        );

        return PriceTaxRateResource::collection(
            $this->priceTaxRateRepository->findMany()
        );
    }

    public function show(Price $price, PriceTaxRate $priceTaxRate, StorePriceTaxRateRequest $request)
    {
        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);

        return new PriceTaxRateResource($priceTaxRate);
    }

    public function store(Price $price, TaxRate $taxRate, Request $request)
    {
        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);

        $check = $price->taxRates()->where('id', $taxRate->id)->exists();
        if ($check) {
            return response()->json([
                'message' => 'PriceTaxRate already exists for this price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->priceTaxRateService->createPriceTaxRate(
            $price,
            [
                $taxRate->id
            ]
        );

        return response()->json([
            'message' => 'PriceTaxRate created',
        ], Response::HTTP_OK);
    }

    public function destroy(Price $price, TaxRate $taxRate, Request $request)
    {
        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);

        $delete = $this->priceTaxRateService->deletePriceTaxRate($price, $taxRate);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting priceTaxRate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PriceTaxRate deleted',
        ], Response::HTTP_OK);
    }
}
