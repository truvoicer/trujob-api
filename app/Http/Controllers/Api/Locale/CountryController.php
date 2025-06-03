<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Resources\Product\CountryResource;
use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Services\Locale\CountryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends Controller
{

    public function __construct(
        private CountryService $countryService,
        private CountryRepository $countryRepository
    )
    {
    }
    public function index(Request $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        $this->countryRepository->setPagination(true);
        $this->countryRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->countryRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->countryRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->countryRepository->setPage(
            $request->get('page', 1)
        );
        $search = $request->get('query', null);
        if ($search) {
            $this->countryRepository->addWhere(
                'name',
                "%$search%",
                'like',
            );
        }

        return CountryResource::collection(
            $this->countryRepository->findMany()
        );
    }

    public function show(Country $country, Request $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        return new CountryResource($country);
    }

    public function store(StoreCountryRequest $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        $create = $this->countryService->createCountry($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating country',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Country created',
        ], Response::HTTP_OK);
    }

    public function update(Country $country, UpdateCountryRequest $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        $update = $this->countryService->updateCountry($country, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating country',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Country updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Country $country, Request $request) {
        $this->countryService->setUser($request->user()->user);
        $this->countryService->setSite($request->user()->site);
        if (!$this->countryService->deleteCountry($country)) {
            return response()->json([
                'message' => 'Error deleting country',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Country deleted',
        ], Response::HTTP_OK);
    }
}
