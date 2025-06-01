<?php

namespace App\Http\Controllers\Api\Tax;

use App\Enums\Order\Tax\TaxRateAmountType;
use App\Http\Controllers\Controller;
use App\Repositories\TaxRateRepository;
use App\Services\Tax\TaxRateService;

class TaxRateAmountTypeController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
        private TaxRateRepository $taxRateRepository,
    )
    {}


    public function index()
    {
        return response()->json([
            'message' => 'Tax rate amount types retrieved successfully',
            'data' => TaxRateAmountType::cases(),
        ]);
    }
}
