<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingColorResource;
use App\Models\Color;
use App\Repositories\ListingColorRepository;
use App\Services\Listing\ListingColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{

    public function __construct(
        private ListingColorService $listingColorService,
        private ListingColorRepository $listingColorRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->listingColorRepository->setPagination(true);
        $this->listingColorRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->listingColorRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingColorRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingColorRepository->setPage(
            $request->get('page', 1)
        );
        
        return ListingColorResource::collection(
            $this->listingColorRepository->findMany()
        );
    }

    public function createColor(Request $request) {
        $this->listingColorService->setUser($request->user());
        $create = $this->listingColorService->createColor($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating color',
                [],
                $this->listingColorService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Color created', [], $this->listingColorService->getErrors());
    }

    public function updateColor(Color $color, Request $request) {
        $this->listingColorService->setUser($request->user());
        $this->listingColorService->setColor($color);
        $update = $this->listingColorService->updateColor($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating color',
                [],
                $this->listingColorService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Color updated', [], $this->listingColorService->getErrors());
    }
    public function deleteColor(Color $color) {
        $this->listingColorService->setColor($color);
        $delete = $this->listingColorService->deleteColor();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting color',
                [],
                $this->listingColorService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Color deleted', [], $this->listingColorService->getErrors());
    }
}
