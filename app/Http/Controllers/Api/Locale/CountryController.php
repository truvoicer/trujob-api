<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locale\StoreCountryRequest;
use App\Http\Requests\Locale\UpdateCountryRequest;
use App\Models\Country;
use App\Services\Locale\CountryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends Controller
{
    protected CountryService $countryService;

    public function __construct(CountryService $countryService, Request $request)
    {
        $this->countryService = $countryService;
    }

    public function createCountryBatch(Request $request) {
        $this->countryService->setUser($request->user());
        $create = $this->countryService->createCountryBatch($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating country batch',
                [],
                $this->countryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Country batch created', [], $this->countryService->getErrors());
    }
    public function createCountry(Request $request) {
        $this->countryService->setUser($request->user());
        $create = $this->countryService->createCountry($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating country',
                [],
                $this->countryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Country created', [], $this->countryService->getErrors());
    }

    public function updateCountry(Country $country, Request $request) {
        $this->countryService->setUser($request->user());
        $this->countryService->setCountry($country);
        $update = $this->countryService->updateCountry($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating country',
                [],
                $this->countryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Country updated', [], $this->countryService->getErrors());
    }
    public function deleteCountry(Country $country, Request $request) {
        $this->countryService->setUser($request->user());
        $this->countryService->setCountry($country);
        if (!$this->countryService->deleteCountry()) {
            return $this->sendErrorResponse(
                'Error deleting country',
                [],
                $this->countryService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Country deleted', [], $this->countryService->getErrors());
    }
}
