<?php

namespace App\Http\Controllers\Api\Tax;

use App\Enums\Order\Tax\TaxScope;
use App\Http\Controllers\Controller;
use App\Repositories\TaxRateRepository;
use App\Services\Tax\TaxRateService;

class TaxRateAbleController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
        private TaxRateRepository $taxRateRepository,
    )
    {}


    public function index()
    {
        return response()->json([
            'message' => 'Tax scopes retrieved successfully',
            'data' => TaxScope::cases(),
        ]);
    }
}
