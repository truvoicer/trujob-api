<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Country\DestroyBulkCountryRequest;
use App\Http\Requests\Country\StoreBulkCountryRequest;
use App\Services\Locale\CountryService;
use Symfony\Component\HttpFoundation\Response;

class BulkCountryController extends Controller
{

    public function __construct(
        private CountryService $countryService
    )
    {
    }

    public function store(StoreBulkCountryRequest $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        $create = $this->countryService->createCountryBatch(
            $request->validated('ids')
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating country batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Country batch created',
        ], Response::HTTP_OK);
    }

    public function destroy(DestroyBulkCountryRequest $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        $delete = $this->countryService->deleteCountryBatch(
            $request->validated('ids')
        );
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting country batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Country batch deleted',
        ], Response::HTTP_OK);
    }

}
