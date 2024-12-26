<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreColorRequest;
use App\Http\Requests\Listing\UpdateColorRequest;
use App\Http\Resources\Listing\ColorCollection;
use App\Models\Color;
use App\Services\Listing\ListingColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{
    protected ListingColorService $listingColorService;

    public function __construct(ListingColorService $colorService, Request $request)
    {
        $this->listingColorService = $colorService;
    }

    public function fetchColors(Request $request) {
        $this->listingColorService->setPagination(true);
        return $this->sendSuccessResponse(
            'Colors fetch',
            ( new ColorCollection($this->listingColorService->colorFetch())),
            $this->listingColorService->getErrors());
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
