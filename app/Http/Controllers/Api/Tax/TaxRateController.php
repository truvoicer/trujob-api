<?php

namespace App\Http\Controllers\Api\Tax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxRate\StoreTaxRateRequest;
use App\Http\Requests\Tax\TaxRate\UpdateTaxRateRequest;
use App\Http\Resources\Tax\TaxRateResource;
use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use App\Services\Tax\TaxRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxRateController extends Controller
{

    public function __construct(
        private TaxRateService $taxRateService,
        private TaxRateRepository $taxRateRepository,
    )
    {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TaxRate $taxRate, Request $request) {
        $this->taxRateRepository->setQuery(
            $taxRate->taxRates()
        );
        $this->taxRateRepository->setPagination(true);
        $this->taxRateRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->taxRateRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->taxRateRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->taxRateRepository->setPage(
            $request->get('page', 1)
        );

        return TaxRateResource::collection(
            $this->taxRateRepository->findMany()
        );
    }

    public function view(TaxRate $taxRate, Request $request) {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);
        
        return new TaxRateResource($taxRate);
    }

    public function create(StoreTaxRateRequest $request) {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);

        if (!$this->taxRateService->createTaxRate($request->validated())) {
            return response()->json([
                'message' => 'Error creating taxRate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'TaxRate created',
        ], Response::HTTP_CREATED);
    }

    
    public function update(TaxRate $taxRate, UpdateTaxRateRequest $request) {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);

        if (!$this->taxRateService->updateTaxRate($taxRate, $request->validated())) {
            return response()->json([
                'message' => 'Error updating taxRate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'TaxRate updated',
        ], Response::HTTP_OK);
    }
    
    public function destroy(TaxRate $taxRate, Request $request) {
        $this->taxRateService->setUser($request->user()->user);
        $this->taxRateService->setSite($request->user()->site);
        
        if (!$this->taxRateService->deleteTaxRate($taxRate)) {
            return response()->json([
                'message' => 'Error deleting taxRate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'TaxRate deleted',
        ], Response::HTTP_OK);
    }
}
