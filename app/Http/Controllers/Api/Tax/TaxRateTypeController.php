<?php

namespace App\Http\Controllers\Api\Tax;

use App\Enums\Order\Tax\TaxRateType;
use App\Http\Controllers\Controller;
use App\Repositories\TaxRateRepository;
use App\Services\Tax\TaxRateService;

class TaxRateTypeController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
        private TaxRateRepository $taxRateRepository,
    )
    {}


    public function index()
    {
        return response()->json([
            'message' => 'Tax rate types retrieved successfully',
            'data' => TaxRateType::cases(),
        ]);
    }
}
