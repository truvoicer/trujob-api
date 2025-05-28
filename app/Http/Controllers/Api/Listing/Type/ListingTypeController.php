<?php

namespace App\Http\Controllers\Api\Listing\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\ListingType\CreateListingTypeRequest;
use App\Http\Requests\Listing\ListingType\EditListingTypeRequest;
use App\Http\Resources\Listing\ListingTypeResource;
use App\Models\ListingType;
use App\Repositories\ListingTypeRepository;
use App\Services\Listing\ListingTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingTypeController extends Controller
{

    public function __construct(
        private ListingTypeService $listingTypeService,
        private ListingTypeRepository $listingTypeRepository
     )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->listingTypeRepository->setPagination(true);
        $this->listingTypeRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingTypeRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingTypeRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingTypeRepository->setPage(
            $request->get('page', 1)
        );
        
        return ListingTypeResource::collection(
            $this->listingTypeRepository->findMany()
        );
    }

    public function store(CreateListingTypeRequest $request) {
        $this->listingTypeService->setUser($request->user()->user);
        $this->listingTypeService->setSite($request->user()->site);
        
        if (!$this->listingTypeService->createListingType($request->validated())) {
            return response()->json([
                'message' => 'Error creating listing type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing type created',
        ], Response::HTTP_CREATED);
    }

    public function update(ListingType $listingType, EditListingTypeRequest $request) {
        $this->listingTypeService->setUser($request->user()->user);
        $this->listingTypeService->setSite($request->user()->site);
        
        if (!$this->listingTypeService->updateListingType($listingType, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing type updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ListingType $listingType, Request $request) {
        $this->listingTypeService->setUser($request->user()->user);
        $this->listingTypeService->setSite($request->user()->site);
        
        if (!$this->listingTypeService->deleteListingType($listingType)) {
            return response()->json([
                'message' => 'Error deleting listing type',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing type deleted',
        ], Response::HTTP_OK);
    }
}
