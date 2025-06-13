<?php

namespace App\Http\Controllers\Api\Price\TaxRate;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Price\PriceTaxRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkPriceTaxRateController extends Controller
{

    public function __construct(
        private PriceTaxRateService $priceTaxRateService,
    )
    {
    }

    public function store(Price $price, Request $request) {
        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('tax_rates')->validate();
        if (
            !$this->priceTaxRateService->attachBulkTaxRatesToPrice(
                $price,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product tax rates',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price tax rates created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Price $price, Request $request) {
        $this->priceTaxRateService->setUser($request->user()->user);
        $this->priceTaxRateService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('tax_rates')->validate();

        if (
            !$this->priceTaxRateService->detachBulkTaxRatesFromPrice(
                $price,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing price tax rates',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price tax rates removed',
        ], Response::HTTP_OK);
    }
}
