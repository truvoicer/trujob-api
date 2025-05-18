<?php
namespace App\Http\Controllers\Api\PriceType;

use App\Http\Controllers\Controller;
use App\Http\Resources\PriceTypeResource;
use App\Models\PriceType;
use App\Repositories\PriceTypeRepository;
use App\Services\PriceType\PriceTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceTypeController extends Controller
{
    // This controller is responsible for handling priceType-related operations.
    // It will contain methods to create, update, delete, and retrieve priceTypes.
    // The methods will interact with the PriceTypeService to perform the necessary operations.
    
    public function __construct(
        private PriceTypeService $priceTypeService,
        private PriceTypeRepository $priceTypeRepository
        
    )
    {
    }

    public function index(Request $request) 
    {
        
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);

        $this->priceTypeRepository->setPagination(true);
        $this->priceTypeRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->priceTypeRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->priceTypeRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->priceTypeRepository->setPage(
            $request->get('page', 1)
        );
        
        return PriceTypeResource::collection(
            $this->priceTypeRepository->findMany()
        );
    }

    public function view(PriceType $priceType, Request $request) 
    {
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);

        return new PriceTypeResource($priceType);
    }

    public function create(Request $request) 
    {
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);

        $create = $this->priceTypeService->createPriceType($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating priceType',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PriceType created',
        ], Response::HTTP_OK);
    }

    public function update(PriceType $priceType, Request $request) 
    {
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);

        $update = $this->priceTypeService->updatePriceType($priceType, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating priceType',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PriceType updated',
        ], Response::HTTP_OK);
    }

    public function destroy(PriceType $priceType, Request $request) 
    {
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);

        $delete = $this->priceTypeService->deletePriceType($priceType);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting priceType',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PriceType deleted',
        ], Response::HTTP_OK);
    }
}