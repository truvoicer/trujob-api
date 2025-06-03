<?php
namespace App\Http\Controllers\Api\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\Price\StorePriceRequest;
use App\Http\Requests\Price\UpdatePriceRequest;
use App\Http\Resources\Price\PriceResource;
use App\Models\Price;
use App\Repositories\PriceRepository;
use App\Services\Price\PriceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceController extends Controller
{
    // This controller is responsible for handling price-related operations.
    // It will contain methods to create, update, delete, and retrieve prices.
    // The methods will interact with the PriceService to perform the necessary operations.
    
    public function __construct(
        private PriceService $priceService,
        private PriceRepository $priceRepository
        
    )
    {
    }

    public function index(Request $request) 
    {
        
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);

        $this->priceRepository->setPagination(true);
        $this->priceRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->priceRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->priceRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->priceRepository->setPage(
            $request->get('page', 1)
        );
        
        return PriceResource::collection(
            $this->priceRepository->findMany()
        );
    }

    public function show(Price $price, Request $request) 
    {
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);

        return new PriceResource($price);
    }

    public function store(StorePriceRequest $request) 
    {
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);

        $create = $this->priceService->createPrice($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price created',
        ], Response::HTTP_OK);
    }

    public function update(Price $price, UpdatePriceRequest $request) 
    {
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);

        $update = $this->priceService->updatePrice($price, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Price $price, Request $request) 
    {
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);

        $delete = $this->priceService->deletePrice($price);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price deleted',
        ], Response::HTTP_OK);
    }
}