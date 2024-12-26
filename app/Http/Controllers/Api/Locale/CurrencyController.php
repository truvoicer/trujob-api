<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locale\StoreCurrencyRequest;
use App\Http\Requests\Locale\UpdateCurrencyRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Services\Locale\CurrencyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService, Request $request)
    {
        $this->currencyService = $currencyService;
    }

    public function createCurrencyBatch(Request $request) {
        $this->currencyService->setUser($request->user());
        $create = $this->currencyService->createCurrencyBatch($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating currency batch',
                [],
                $this->currencyService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Currency batch created', [], $this->currencyService->getErrors());
    }

    public function createCurrency(Country $country, Request $request) {
        $this->currencyService->setUser($request->user());
        $this->currencyService->setCountry($country);
        $create = $this->currencyService->createCurrency($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating currency',
                [],
                $this->currencyService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Currency created', [], $this->currencyService->getErrors());
    }

    public function updateCurrency(Currency $currency, Request $request) {
        $this->currencyService->setUser($request->user());
        $this->currencyService->setCurrency($currency);
        $update = $this->currencyService->updateCurrency($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating currency',
                [],
                $this->currencyService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Currency updated', [], $this->currencyService->getErrors());
    }
    public function deleteCurrency(Currency $currency, Request $request) {
        $this->currencyService->setUser($request->user());
        $this->currencyService->setCurrency($currency);
        if (!$this->currencyService->deleteCurrency()) {
            return $this->sendErrorResponse(
                'Error deleting currency',
                [],
                $this->currencyService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Currency deleted', [], $this->currencyService->getErrors());
    }
}
