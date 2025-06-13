<?php

namespace App\Http\Controllers\Api\Tax;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Tax\TaxRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkTaxRateController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
    ) {}

    public function destroy(Price $price, Request $request)
    {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('tax_rates')->validate();
        if (
            !$this->taxRateService->destroyBulkTaxRates(
                $request->get('ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error removing tax rates',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Tax rates removed',
        ], Response::HTTP_OK);
    }
}
