<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\StoreCurrencyRequest as CurrencyStoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest as CurrencyUpdateCurrencyRequest;
use App\Http\Resources\Product\CurrencyResource;
use App\Models\Country;
use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use App\Services\Locale\CurrencyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{

    public function __construct(
        private CurrencyService $currencyService,
        private CurrencyRepository $currencyRepository
    )
    {
    }

    public function index(Request $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);

        $this->currencyRepository->setPagination(true);
        $this->currencyRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->currencyRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->currencyRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->currencyRepository->setPage(
            $request->get('page', 1)
        );
        $search = $request->get('query', null);
        if ($search) {
            $this->currencyRepository->addWhere(
                'name',
                "%$search%",
                'like',
            );
        }
        
        return CurrencyResource::collection(
            $this->currencyRepository->findMany()
        );
    }

    public function show(Currency $currency, Request $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);

        return new CurrencyResource($currency);
    }

    public function store(CurrencyStoreCurrencyRequest $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);

        $create = $this->currencyService->createCurrency($country, $request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating currency',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency created',
        ], Response::HTTP_OK);
    }

    public function update(Currency $currency, CurrencyUpdateCurrencyRequest $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);
        
        $update = $this->currencyService->updateCurrency($currency, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating currency',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Currency $currency, Request $request) {
        $this->currencyService->setUser($request->user()->user);
        $this->currencyService->setSite($request->user()->site);

        if (!$this->currencyService->deleteCurrency($currency)) {
            return response()->json([
                'message' => 'Error deleting currency',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Currency deleted',
        ], Response::HTTP_OK);
    }
}
