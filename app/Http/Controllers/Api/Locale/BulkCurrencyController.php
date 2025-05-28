<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\StoreCurrencyRequest as CurrencyStoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest as CurrencyUpdateCurrencyRequest;
use App\Http\Requests\Locale\StoreBulkCurrencyRequest;
use App\Http\Requests\Locale\StoreCurrencyRequest;
use App\Http\Requests\Locale\UpdateCurrencyRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Services\Locale\CurrencyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkCurrencyController extends Controller
{

    public function __construct(
        private CurrencyService $currencyService
    )
    {
    }

    public function store(StoreBulkCurrencyRequest $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);
        $create = $this->currencyService->createCurrencyBatch($request->all());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating currency batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency batch created',
        ], Response::HTTP_OK);
    }

}
