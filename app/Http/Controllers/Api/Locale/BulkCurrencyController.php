<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\DestroyBulkCurrencyRequest;
use App\Http\Requests\Currency\StoreBulkCurrencyRequest;
use App\Services\Locale\CurrencyService;
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
        $create = $this->currencyService->createCurrencyBatch(
            $request->validated('currencies')
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating currency batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency batch created',
        ], Response::HTTP_OK);
    }

    public function destroy(DestroyBulkCurrencyRequest $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);
        $delete = $this->currencyService->deleteCurrencyBatch(
            $request->validated('ids')
        );
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting currency batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency batch deleted',
        ], Response::HTTP_OK);
    }

}
