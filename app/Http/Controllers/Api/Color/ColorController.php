<?php

namespace App\Http\Controllers\Api\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\StoreColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Http\Resources\Product\ColorResource;
use App\Models\Color;
use App\Repositories\ColorRepository;
use App\Services\Color\ColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{

    public function __construct(
        private ColorService $colorService,
        private ColorRepository $colorRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->colorRepository->setPagination(true);
        $this->colorRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->colorRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->colorRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->colorRepository->setPage(
            $request->get('page', 1)
        );
        
        return ColorResource::collection(
            $this->colorRepository->findMany()
        );
    }

    public function store(StoreColorRequest $request) {
        $this->colorService->setUser($request->user()->user);
        $this->colorService->setSite($request->user()->site);
        
        if (!$this->colorService->createColor($request->validated())) {
            return response()->json([
                'message' => 'Error creating color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Color created',
        ], Response::HTTP_CREATED);
    }

    public function update(Color $color, UpdateColorRequest $request) {
        $this->colorService->setUser($request->user()->user);
        $this->colorService->setSite($request->user()->site);
        
        if (!$this->colorService->updateColor($color, $request->validated())) {
            return response()->json([
                'message' => 'Error updating color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Color updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Color $color, Request $request) {
        $this->colorService->setUser($request->user()->user);
        $this->colorService->setSite($request->user()->site);

        if (!$this->colorService->deleteColor($color)) {
            return response()->json([
                'message' => 'Error deleting color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Color deleted',
        ], Response::HTTP_OK);
    }
}
