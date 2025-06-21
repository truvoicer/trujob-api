<?php

namespace App\Http\Controllers\Api\Tax;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use App\Services\Tax\TaxRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxRateSetAsDefaultController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
        private TaxRateRepository $taxRateRepository,
    ) {}

    public function store(TaxRate $taxRate, Request $request)
    {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);

        $this->taxRateService->setAsDefault($taxRate);

        return response()->json([
            'message' => 'TaxRate created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(TaxRate $taxRate, Request $request)
    {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);

        $this->taxRateService->removeAsDefault($taxRate);

        return response()->json([
            'message' => 'TaxRate removed as default',
        ], Response::HTTP_OK);
    }
}
